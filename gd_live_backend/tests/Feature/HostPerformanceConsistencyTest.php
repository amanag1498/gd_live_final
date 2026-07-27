<?php

namespace Tests\Feature;

use App\Models\Agency;
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
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HostPerformanceConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        Carbon::setTestNow(Carbon::parse('2026-07-27 08:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_app_admin_report_and_payout_use_the_same_completed_week_totals(): void
    {
        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency');
        $agency = Agency::query()->create([
            'owner_user_id' => $agencyOwner->id,
            'name' => 'Diljaan Agency',
        ]);

        $hostUser = User::factory()->create(['name' => 'Dj_Dewanee']);
        $hostUser->assignRole('host');
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'diwanee',
        ]);

        $mondayRoom = LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'monday-room',
            'title' => 'Monday Room',
            'room_type' => 'video',
            'status' => 'ended',
            'started_at' => '2026-07-20 00:00:00',
            'ended_at' => '2026-07-20 16:36:00',
            'last_activity_at' => '2026-07-20 16:36:00',
        ]);
        $sundayRoom = LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'sunday-room',
            'title' => 'Sunday Room',
            'room_type' => 'video',
            'status' => 'live',
            'started_at' => '2026-07-26 10:00:00',
            'last_activity_at' => '2026-07-26 14:18:00',
        ]);

        $regularGift = Gift::query()->create([
            'name' => 'Room Gift',
            'coins' => 1,
            'gift_url' => 'https://example.com/room.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $pkGift = Gift::query()->create([
            'name' => 'PK Gift',
            'coins' => 1,
            'gift_url' => 'https://example.com/pk.png',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $sender = User::factory()->create();

        $this->createGiftLedger(
            room: $mondayRoom,
            gift: $regularGift,
            sender: $sender,
            host: $host,
            agency: $agency,
            coins: 50_001,
            occurredAt: '2026-07-20 12:00:00',
            reference: 'room-gift-monday',
        );
        $this->createGiftLedger(
            room: $sundayRoom,
            gift: $regularGift,
            sender: $sender,
            host: $host,
            agency: $agency,
            coins: 31_000,
            occurredAt: '2026-07-26 12:00:00',
            reference: 'room-gift-sunday',
        );
        $pkTransaction = $this->createGiftLedger(
            room: $mondayRoom,
            gift: $pkGift,
            sender: $sender,
            host: $host,
            agency: $agency,
            coins: 5_500,
            occurredAt: '2026-07-20 13:00:00',
            reference: 'pk-gift',
        );

        $opponentUser = User::factory()->create();
        $opponent = Host::query()->create([
            'user_id' => $opponentUser->id,
            'stage_name' => 'Opponent',
        ]);
        $opponentRoom = LiveRoom::query()->create([
            'host_id' => $opponent->id,
            'room_id' => 'opponent-room',
            'title' => 'Opponent Room',
            'room_type' => 'video',
            'status' => 'ended',
            'started_at' => '2026-07-20 12:30:00',
            'ended_at' => '2026-07-20 13:30:00',
            'last_activity_at' => '2026-07-20 13:30:00',
        ]);
        $battle = LiveRoomPkBattle::query()->create([
            'battle_id' => 'weekly-pk',
            'room_a_id' => $mondayRoom->id,
            'room_b_id' => $opponentRoom->id,
            'host_a_id' => $host->id,
            'host_b_id' => $opponent->id,
            'invited_by_host_id' => $host->id,
            'status' => 'ended',
            'started_at' => '2026-07-20 12:30:00',
            'ended_at' => '2026-07-20 13:30:00',
        ]);
        $pkEvent = LiveRoomPkEvent::query()->create([
            'pk_battle_id' => $battle->id,
            'room_id' => $mondayRoom->id,
            'user_id' => $sender->id,
            'event_type' => 'gift',
            'coins' => 5_500,
            'wallet_transaction_id' => $pkTransaction->id,
            'gift_id' => $pkGift->id,
        ]);
        $pkEvent->forceFill([
            'created_at' => '2026-07-20 13:00:00',
            'updated_at' => '2026-07-20 13:00:00',
        ])->save();

        $caller = User::factory()->create();
        $call = CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $hostUser->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'type' => 'video',
            'status' => 'ended',
            'started_at' => '2026-07-22 10:00:00',
            'accepted_at' => '2026-07-22 10:00:00',
            'ended_at' => '2026-07-22 11:33:00',
            'duration_seconds' => 5_580,
            'billable_minutes' => 93,
            'coin_rate_per_minute' => 200,
            'total_coins_charged' => 18_600,
            'host_earning' => 18_600,
            'agency_earning' => 0,
            'platform_earning' => 0,
        ]);
        $callLedger = CallEarningLedger::query()->create([
            'call_session_id' => $call->id,
            'caller_id' => $caller->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'total_coins' => 18_600,
            'host_earning' => 18_600,
            'agency_earning' => 0,
            'platform_earning' => 0,
            'duration_seconds' => 5_580,
            'billable_minutes' => 93,
        ]);
        $callLedger->forceFill([
            'created_at' => '2026-07-22 11:33:00',
            'updated_at' => '2026-07-22 11:33:00',
        ])->save();

        Sanctum::actingAs($hostUser);
        $this->getJson('/api/profile/host-earnings-report')
            ->assertOk()
            ->assertJsonPath('data.last_week.summary.total_video_room_minutes', 1_254)
            ->assertJsonPath('data.last_week.summary.video_room_gifts_coins', 81_001)
            ->assertJsonPath('data.last_week.summary.pk_gift_coins', 5_500)
            ->assertJsonPath('data.last_week.summary.total_gifted_coins', 86_501)
            ->assertJsonPath('data.last_week.summary.video_call_minutes', 93)
            ->assertJsonPath('data.last_week.summary.video_call_earnings', 18_600);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $reportResponse = $this->actingAs($admin)->get(route('admin.reports.hosts', [
            'host_id' => $host->id,
            'range' => 'weekly',
            'from' => '2026-07-20',
            'to' => '2026-07-26',
        ]))->assertOk();
        $weeklyRow = collect($reportResponse->viewData('rows'))->firstWhere('host_id', $host->id);

        $this->assertSame(1_254, $weeklyRow['duration_min']);
        $this->assertSame(81_001, $weeklyRow['room_gift_coins']);
        $this->assertSame(5_500, $weeklyRow['pk_coins']);
        $this->assertSame(18_600, $weeklyRow['video_call_coins']);
        $this->assertSame(105_101, $weeklyRow['gross_coins']);

        $payoutService = app(AgencyWeeklyPayoutReportService::class);
        [$periodStart, $periodEnd] = $payoutService->resolvePeriod('2026-07-20', '2026-07-26');
        $payout = $payoutService->generate($periodStart, $periodEnd, $agency->id, true)['reports'][0];
        $payoutItem = $payout->items()->where('host_id', $host->id)->firstOrFail();

        $this->assertSame(1_254, $payoutItem->video_room_minutes);
        $this->assertSame(81_001, $payoutItem->video_gift_coins);
        $this->assertSame(5_500, $payoutItem->pk_gift_coins);
        $this->assertSame(18_600, $payoutItem->video_call_coins);
        $this->assertSame(105_101, $payoutItem->total_coins);
    }

    private function createGiftLedger(
        LiveRoom $room,
        Gift $gift,
        User $sender,
        Host $host,
        Agency $agency,
        int $coins,
        string $occurredAt,
        string $reference,
    ): WalletTransaction {
        $transaction = WalletTransaction::query()->create([
            'wallet_id' => $sender->wallet->id,
            'type' => 'debit',
            'coins' => $coins,
            'category' => 'gift',
            'reference' => $reference,
            'description' => 'Host performance consistency test gift',
            'balance_before' => $coins,
            'balance_after' => 0,
        ]);
        $transaction->forceFill([
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ])->save();

        $roomGift = LiveRoomGift::query()->create([
            'live_room_id' => $room->id,
            'gift_id' => $gift->id,
            'sender_user_id' => $sender->id,
            'quantity' => 1,
            'coins_per_unit' => $coins,
            'total_coins' => $coins,
            'transaction_id' => (string) $transaction->id,
        ]);
        $roomGift->forceFill([
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ])->save();

        $ledger = LiveRoomGiftEarningLedger::query()->create([
            'live_room_gift_id' => $roomGift->id,
            'live_room_id' => $room->id,
            'sender_user_id' => $sender->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'total_coins' => $coins,
            'host_payout_coins' => $coins,
            'agency_payout_coins' => 0,
            'platform_revenue_coins' => 0,
        ]);
        $ledger->forceFill([
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ])->save();

        return $transaction;
    }
}
