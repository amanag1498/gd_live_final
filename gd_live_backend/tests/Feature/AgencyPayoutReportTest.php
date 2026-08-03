<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\AgencyPayoutReport;
use App\Models\CallEarningLedger;
use App\Models\CallSession;
use App\Models\Gift;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomGift;
use App\Models\LiveRoomGiftEarningLedger;
use App\Models\LiveRoomPkBattle;
use App\Models\LiveRoomPkEvent;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\AgencyWeeklyPayoutReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgencyPayoutReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-04-23 12:00:00', config('app.timezone')));

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_command_generates_reports_idempotently_for_all_agencies(): void
    {
        [$agency, $owner, $host] = $this->seedAgencyFixture();
        $emptyOwner = User::factory()->create();
        $emptyOwner->assignRole('agency');
        Agency::query()->create([
            'owner_user_id' => $emptyOwner->id,
            'name' => 'Zero Orbit',
        ]);

        Artisan::call('agency:payout-reports:generate', [
            '--start' => '2026-04-21',
            '--end' => '2026-04-27',
        ]);

        $this->assertDatabaseCount('agency_payout_reports', 3);

        /** @var AgencyPayoutReport $report */
        $report = AgencyPayoutReport::query()
            ->where('agency_id', $agency->id)
            ->with('items')
            ->firstOrFail();

        $this->assertSame(150, $report->gross_earnings);
        $this->assertSame(0, $report->platform_commission);
        $this->assertSame(0, $report->agency_commission);
        $this->assertSame(150, $report->host_share);
        $this->assertSame(150, $report->final_payable);
        $this->assertSame(1, $report->total_hosts);
        $this->assertSame(1, $report->active_hosts_count);
        $this->assertSame(100, $report->total_call_earnings);
        $this->assertSame(0, $report->total_gift_earnings);
        $this->assertSame(0, $report->total_live_room_earnings);
        $this->assertSame(50, $report->total_pk_earnings);

        $item = $report->items->firstOrFail();
        $this->assertSame($host->id, $item->host_id);
        $this->assertSame(100, $item->call_earnings);
        $this->assertSame(0, $item->gift_earnings);
        $this->assertSame(0, $item->live_room_earnings);
        $this->assertSame(50, $item->pk_earnings);

        Artisan::call('agency:payout-reports:generate', [
            '--start' => '2026-04-21',
            '--end' => '2026-04-27',
        ]);

        $this->assertDatabaseCount('agency_payout_reports', 3);
    }

    public function test_force_regeneration_replaces_unpaid_report_and_paid_report_cannot_be_paid_twice(): void
    {
        [$agency, $owner] = $this->seedAgencyFixture();

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');

        $first = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $this->assertSame(150, $first->final_payable);

        CallEarningLedger::query()->create([
            'call_session_id' => CallSession::query()->create([
                'caller_id' => User::factory()->create()->id,
                'receiver_id' => $agency->hosts()->firstOrFail()->user_id,
                'host_id' => $agency->hosts()->firstOrFail()->id,
                'agency_id' => $agency->id,
                'type' => 'video',
                'status' => 'ended',
                'coin_rate_per_minute' => 20,
                'billable_minutes' => 1,
                'total_coins_charged' => 20,
                'host_earning' => 12,
                'agency_earning' => 2,
                'platform_earning' => 6,
            ])->id,
            'caller_id' => User::factory()->create()->id,
            'host_id' => $agency->hosts()->firstOrFail()->id,
            'agency_id' => $agency->id,
            'total_coins' => 20,
            'host_earning' => 12,
            'agency_earning' => 2,
            'platform_earning' => 6,
            'duration_seconds' => 60,
            'billable_minutes' => 1,
            'created_at' => '2026-04-24 10:00:00',
            'updated_at' => '2026-04-24 10:00:00',
        ]);

        $forced = $service->generate($start, $end, $agency->id, true)['reports'][0];
        $this->assertSame(170, $forced->gross_earnings);
        $this->assertSame(170, $forced->final_payable);
        $this->assertDatabaseCount('agency_payout_reports', 1);

        $service->approve($forced, 2, 'Approved');
        $service->publish($forced, 'Published for payment');
        $paid = $service->markPaid($forced, 'Paid');
        $owner->refresh();
        $this->assertSame('paid', $paid->status);
        $this->assertSame(0, (int) $owner->wallet->fresh()->balance);
        $this->assertDatabaseMissing('wallet_transactions', [
            'wallet_id' => $owner->wallet->id,
            'reference' => 'agency_payout_report:' . $paid->id,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $service->markPaid($paid, 'Duplicate pay');
    }

    public function test_paid_report_cannot_be_regenerated_with_force(): void
    {
        [$agency] = $this->seedAgencyFixture();

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $service->approve($report, 0, 'Approved');
        $service->publish($report, 'Published for payment');
        $service->markPaid($report, 'Paid offline');

        $this->expectException(\InvalidArgumentException::class);
        $service->generate($start, $end, $agency->id, true);
    }

    public function test_transferred_host_room_time_and_earnings_stay_with_the_historical_agency(): void
    {
        [$historicalAgency, , $host] = $this->seedAgencyFixture();
        $currentOwner = User::factory()->create();
        $currentOwner->assignRole('agency');
        $currentAgency = Agency::query()->create([
            'owner_user_id' => $currentOwner->id,
            'name' => 'Current Agency',
        ]);

        $host->update(['agency_id' => $currentAgency->id]);

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');

        $historicalReport = $service->generate($start, $end, $historicalAgency->id)['reports'][0];
        $historicalItem = $historicalReport->items()->where('host_id', $host->id)->firstOrFail();

        $this->assertSame(150, $historicalItem->total_coins);
        $this->assertSame(30, $historicalItem->video_room_minutes);

        $currentReport = $service->generate($start, $end, $currentAgency->id)['reports'][0];

        $this->assertFalse($currentReport->items()->where('host_id', $host->id)->exists());
        $this->assertSame(0, $currentReport->total_coins);
        $this->assertSame(0, (int) data_get($currentReport->meta, 'totals.video_room_minutes'));
    }

    public function test_amounts_cannot_be_modified_after_approval(): void
    {
        [$agency] = $this->seedAgencyFixture();

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $service->approve($report, 1, 'Approved');

        $this->expectException(\InvalidArgumentException::class);
        $service->markPendingReview($report, 5, 'Should fail');
    }

    public function test_admin_can_update_a_host_row_without_a_page_redirect(): void
    {
        [$agency] = $this->seedAgencyFixture();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $service->approve($report, 0, 'Approved before correction');
        $item = $report->fresh('items')->items->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.show', $report))
            ->assertOk()
            ->assertSee('js-payout-row-form', false)
            ->assertSee("'X-Requested-With': 'XMLHttpRequest'", false)
            ->assertSee("field.addEventListener('change'", false)
            ->assertSee("form.requestSubmit()", false)
            ->assertSee('payout-grid-scroll', false)
            ->assertSee('payout-grid-sticky-left', false)
            ->assertSee('payout-grid-sticky-right', false)
            ->assertSee('data-grid-total="total_coins"', false)
            ->assertSee('const recalculateGrid', false);

        $response = $this->actingAs($admin)->postJson(
            route('admin.agency-payout-reports.items.update', [$report, $item]),
            [
                'video_room_minutes' => $item->video_room_minutes + 5,
                'video_gift_coins' => $item->video_gift_coins,
                'pk_gift_coins' => $item->pk_gift_coins,
                'video_call_coins' => $item->video_call_coins,
                'video_call_minutes' => $item->video_call_minutes,
                'bonus_coins' => 25,
                'host_payout_inr' => 125.50,
                'agency_commission_inr' => 20.25,
                'admin_note' => 'Corrected without reloading',
            ],
        );

        $response->assertOk()
            ->assertJsonPath('report.status', 'pending_review')
            ->assertJsonPath('report.published', false)
            ->assertJsonPath('report.actions.can_approve', true)
            ->assertJsonPath('report.actions.can_publish', false)
            ->assertJsonPath('item.id', $item->id)
            ->assertJsonPath('item.bonus_coins', 25)
            ->assertJsonPath('item.total_inr', 145.75)
            ->assertJsonPath('item.admin_note', 'Corrected without reloading');

        $report->refresh();
        $this->assertSame('pending_review', $report->status);
        $this->assertNull($report->approved_at);
        $this->assertNull($report->published_at);
        $this->assertSame(25, $item->fresh()->bonus_coins);
    }

    public function test_admin_can_delete_an_ordered_host_row_and_report_totals_are_recalculated(): void
    {
        [$agency, , $host] = $this->seedAgencyFixture();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $item = $report->fresh('items')->items->firstOrFail();

        $this->assertSame(
            $report->items->pluck('host_id')->sort()->values()->all(),
            $report->items->pluck('host_id')->values()->all(),
        );

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.show', $report))
            ->assertOk()
            ->assertSee('Host ID / Host')
            ->assertSee('#'.$host->id.' ·', false)
            ->assertSee('js-payout-row-delete-form', false)
            ->assertSee(route('admin.agency-payout-reports.items.destroy', [$report, $item]), false);

        $response = $this->actingAs($admin)->deleteJson(
            route('admin.agency-payout-reports.items.destroy', [$report, $item]),
        );

        $response->assertOk()
            ->assertJsonPath('deleted_item_id', $item->id)
            ->assertJsonPath('report.status', 'pending_review')
            ->assertJsonPath('report.totals.total_hosts', 0)
            ->assertJsonPath('report.totals.total_coins', 0)
            ->assertJsonPath('report.totals.total_video_room_minutes', 0);

        $this->assertDatabaseMissing('agency_payout_report_items', ['id' => $item->id]);
        $this->assertDatabaseHas('admin_action_audits', [
            'admin_user_id' => $admin->id,
            'target_user_id' => $host->user_id,
            'action' => 'delete_item',
            'entity_id' => $report->id,
        ]);
    }

    public function test_invalid_generate_input_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->from(route('admin.agency-payout-reports.index'))
            ->post(route('admin.agency-payout-reports.generate'), [
                'start' => '2026-04-27',
                'end' => '2026-04-21',
            ]);

        $response->assertRedirect(route('admin.agency-payout-reports.index'));
        $response->assertSessionHasErrors(['generate']);

        $exitCode = Artisan::call('agency:payout-reports:generate', [
            '--start' => '2026-04-27',
            '--end' => '2026-04-21',
        ]);

        $this->assertSame(1, $exitCode);
    }

    public function test_agency_export_and_dashboard_preview_are_properly_scoped(): void
    {
        [$agency, $owner] = $this->seedAgencyFixture();
        $otherOwner = User::factory()->create();
        $otherOwner->assignRole('agency');
        Agency::query()->create([
            'owner_user_id' => $otherOwner->id,
            'name' => 'Other Agency',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $report = $service->approve($report, 0, 'Approved for PDF test', $admin);
        $report = $service->publish($report, 'Published for PDF test', $admin);
        app()->instance('dompdf.wrapper', new class {
            public function loadView(string $view, array $data): static
            {
                return $this;
            }

            public function setPaper(string $paper, string $orientation): static
            {
                return $this;
            }

            public function output(): string
            {
                return '%PDF-1.4 test';
            }
        });

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.preview', $report))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename="agency-payout-report-' . $report->id . '.pdf"');

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.export', $report))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="agency-payout-report-' . $report->id . '.pdf"');

        $this->actingAs($owner)
            ->get(route('agency.payout-reports.preview', $report))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename="my-agency-payout-report-' . $report->id . '.pdf"');

        $this->actingAs($owner)
            ->get(route('agency.payout-reports.export', $report))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($otherOwner)
            ->get(route('agency.payout-reports.export', $report))
            ->assertForbidden();

        $this->actingAs($otherOwner)
            ->get(route('agency.payout-reports.preview', $report))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.show', $report))
            ->assertOk()
            ->assertSee(route('admin.agency-payout-reports.preview', $report), false);

        $this->actingAs($owner)
            ->get(route('agency.payout-reports.show', $report))
            ->assertOk()
            ->assertSee(route('agency.payout-reports.preview', $report), false);

        $this->actingAs($admin)
            ->get(route('admin.agencies.dashboard', $agency))
            ->assertOk()
            ->assertSee('Agency Dashboard')
            ->assertSee('Orbit Agency');
    }

    public function test_admin_and_agency_views_are_scoped(): void
    {
        [$agency, $owner, $host] = $this->seedAgencyFixture();
        $otherOwner = User::factory()->create();
        $otherOwner->assignRole('agency');
        Agency::query()->create([
            'owner_user_id' => $otherOwner->id,
            'name' => 'Other Agency',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $service = app(AgencyWeeklyPayoutReportService::class);
        [$start, $end] = $service->resolvePeriod('2026-04-21', '2026-04-27');
        $report = $service->generate($start, $end, $agency->id, false)['reports'][0];
        $report = $service->approve($report, 0, 'Approved for agency visibility test', $admin);
        $report = $service->publish($report, 'Published for agency visibility test', $admin);

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.index'))
            ->assertOk()
            ->assertSee('Weekly Agency Payout Reports')
            ->assertDontSee('onclick="this.showPicker()"', false)
            ->assertDontSee('readonly', false);

        $this->actingAs($admin)
            ->get(route('admin.agency-payout-reports.show', $report))
            ->assertOk()
            ->assertSee('Host Settlement Grid')
            ->assertSee('User ID: '.$host->user_id);

        $this->actingAs($owner)
            ->get(route('agency.payout-reports.index'))
            ->assertOk()
            ->assertSee('Weekly Payout Reports');

        $this->actingAs($owner)
            ->get(route('agency.payout-reports.show', $report))
            ->assertOk()
            ->assertSee('Payout Report #' . $report->id)
            ->assertSee('User #'.$host->user_id);

        $this->actingAs($otherOwner)
            ->get(route('agency.payout-reports.show', $report))
            ->assertForbidden();
    }

    private function seedAgencyFixture(): array
    {
        $owner = User::factory()->create(['name' => 'Agency Owner']);
        $owner->assignRole('agency');

        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);

        $hostUser = User::factory()->create(['name' => 'Host Nova']);
        $hostUser->assignRole('host');
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'Nova',
        ]);

        $caller = User::factory()->create();
        $call = CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $hostUser->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'type' => 'video',
            'status' => 'ended',
            'coin_rate_per_minute' => 20,
            'billable_minutes' => 5,
            'total_coins_charged' => 100,
            'host_earning' => 60,
            'agency_earning' => 10,
            'platform_earning' => 30,
        ]);

        CallEarningLedger::query()->create([
            'call_session_id' => $call->id,
            'caller_id' => $caller->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'total_coins' => 100,
            'host_earning' => 60,
            'agency_earning' => 10,
            'platform_earning' => 30,
            'duration_seconds' => 300,
            'billable_minutes' => 5,
            'created_at' => '2026-04-23 10:00:00',
            'updated_at' => '2026-04-23 10:00:00',
        ]);

        $room = LiveRoom::query()->create([
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'room_id' => 'orbit-room-1',
            'title' => 'Orbit Live',
            'room_type' => 'video',
            'status' => 'ended',
            'started_at' => '2026-04-23 09:00:00',
            'ended_at' => '2026-04-23 09:30:00',
            'last_activity_at' => '2026-04-23 09:30:00',
        ]);

        $gift = Gift::query()->create([
            'name' => 'Rose',
            'coins' => 50,
            'gift_url' => 'https://example.com/rose.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $sender = User::factory()->create();
        $walletTx = WalletTransaction::query()->create([
            'wallet_id' => $sender->wallet->id,
            'type' => 'debit',
            'coins' => 50,
            'category' => 'gift',
            'reference' => 'pk-gift-1',
            'description' => 'PK gift spend',
            'balance_before' => 100,
            'balance_after' => 50,
        ]);

        $roomGift = LiveRoomGift::query()->create([
            'live_room_id' => $room->id,
            'gift_id' => $gift->id,
            'sender_user_id' => $sender->id,
            'quantity' => 1,
            'coins_per_unit' => 50,
            'total_coins' => 50,
            'transaction_id' => (string) $walletTx->id,
            'meta' => ['reference' => 'pk-gift-1'],
        ]);

        LiveRoomGiftEarningLedger::query()->create([
            'live_room_gift_id' => $roomGift->id,
            'live_room_id' => $room->id,
            'sender_user_id' => $sender->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'total_coins' => 50,
            'host_payout_coins' => 30,
            'agency_payout_coins' => 5,
            'platform_revenue_coins' => 15,
            'created_at' => '2026-04-23 11:00:00',
            'updated_at' => '2026-04-23 11:00:00',
        ]);

        $otherOwner = User::factory()->create();
        $otherAgency = Agency::query()->create([
            'owner_user_id' => $otherOwner->id,
            'name' => 'Rival Agency',
        ]);
        $otherHostUser = User::factory()->create();
        $otherHost = Host::query()->create([
            'user_id' => $otherHostUser->id,
            'agency_id' => $otherAgency->id,
            'stage_name' => 'Rival',
        ]);
        $otherRoom = LiveRoom::query()->create([
            'host_id' => $otherHost->id,
            'agency_id' => $otherAgency->id,
            'room_id' => 'rival-room-1',
            'title' => 'Rival Live',
            'room_type' => 'video',
            'status' => 'ended',
            'started_at' => '2026-04-23 09:00:00',
            'ended_at' => '2026-04-23 09:30:00',
            'last_activity_at' => '2026-04-23 09:30:00',
        ]);

        $battle = LiveRoomPkBattle::query()->create([
            'battle_id' => 'pk-orbit-1',
            'room_a_id' => $room->id,
            'room_b_id' => $otherRoom->id,
            'host_a_id' => $host->id,
            'host_b_id' => $otherHost->id,
            'invited_by_host_id' => $host->id,
            'status' => 'ended',
            'duration_seconds' => 120,
            'score_a' => 50,
            'score_b' => 0,
            'started_at' => '2026-04-23 11:00:00',
            'ended_at' => '2026-04-23 11:02:00',
        ]);

        LiveRoomPkEvent::query()->create([
            'pk_battle_id' => $battle->id,
            'room_id' => $room->id,
            'user_id' => $sender->id,
            'event_type' => 'gift',
            'coins' => 50,
            'wallet_transaction_id' => $walletTx->id,
            'gift_id' => $gift->id,
            'metadata' => ['wallet_reference' => 'pk-gift-1'],
        ]);

        return [$agency, $owner, $host];
    }
}
