<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\CallEarningLedger;
use App\Models\CallSession;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomParticipant;
use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\Wallet;
use App\Services\BillingReconciliationService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReportPerformanceRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        config(['app.timezone' => 'Asia/Kolkata']);
    }

    public function test_weekly_host_report_keeps_room_and_participant_totals(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $hostUser = User::factory()->create(['name' => 'Report Host']);
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'stage_name' => 'Report Host',
        ]);
        $viewer = User::factory()->create();
        $room = LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'report-performance-room',
            'title' => 'Report Performance Room',
            'status' => 'ended',
            'started_at' => CarbonImmutable::parse('2026-08-10 10:00:00', 'Asia/Kolkata'),
            'ended_at' => CarbonImmutable::parse('2026-08-10 10:30:00', 'Asia/Kolkata'),
            'last_activity_at' => CarbonImmutable::parse('2026-08-10 10:30:00', 'Asia/Kolkata'),
        ]);
        LiveRoomParticipant::query()->create([
            'live_room_id' => $room->id,
            'user_id' => $viewer->id,
            'session_id' => 'report-viewer-session',
            'role' => 'viewer',
            'joined_at' => CarbonImmutable::parse('2026-08-10 10:05:00', 'Asia/Kolkata'),
            'left_at' => CarbonImmutable::parse('2026-08-10 10:20:00', 'Asia/Kolkata'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.hosts', [
            'range' => 'weekly',
            'from' => '2026-08-10',
            'to' => '2026-08-16',
        ]));

        $response->assertOk();
        $row = collect($response->viewData('rows'))->firstWhere('host_id', $host->id);
        $this->assertNotNull($row);
        $this->assertSame(1, $row['rooms']);
        $this->assertSame(30, $row['duration_min']);
        $this->assertSame(1, $row['participants_total']);
        $this->assertSame(1, $row['participants_unique']);
    }

    public function test_reconciliation_uses_a_fixed_number_of_queries_and_preserves_anomalies(): void
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Performance Agency',
        ]);
        $hostUser = User::factory()->create();
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'Performance Host',
        ]);
        $caller = User::factory()->create();
        $callerWallet = Wallet::query()->where('user_id', $caller->id)->firstOrFail();

        for ($index = 1; $index <= 20; $index++) {
            $call = $this->endedBilledCall($caller, $hostUser, $host, $agency);

            if ($index < 20) {
                CallEarningLedger::query()->create([
                    'call_session_id' => $call->id,
                    'caller_id' => $caller->id,
                    'host_id' => $host->id,
                    'agency_id' => $agency->id,
                    'total_coins' => 20,
                    'host_earning' => 12,
                    'agency_earning' => 2,
                    'platform_earning' => 6,
                    'duration_seconds' => 60,
                    'billable_minutes' => 1,
                ]);
                $this->insertWalletTransaction($callerWallet, [
                    'type' => 'debit',
                    'coins' => 20,
                    'category' => 'video_call',
                    'reference' => 'call_billing:'.$call->id.':1',
                ]);
            }
        }

        $successfulOrder = PaymentOrder::query()->create([
            'user_id' => $caller->id,
            'order_id' => 'performance-success',
            'amount_rupees' => 100,
            'coins' => 100,
            'bonus_coins' => 0,
            'total_coins' => 100,
            'status' => 'success',
            'gateway' => 'mock',
        ]);
        $this->insertWalletTransaction($callerWallet, [
            'type' => 'credit',
            'coins' => 90,
            'category' => 'recharge',
            'reference_type' => 'payment_order',
            'reference_id' => $successfulOrder->id,
        ]);

        PaymentOrder::query()->create([
            'user_id' => $caller->id,
            'order_id' => 'performance-missing',
            'amount_rupees' => 50,
            'coins' => 50,
            'bonus_coins' => 0,
            'total_coins' => 50,
            'status' => 'success',
            'gateway' => 'mock',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $anomalies = app(BillingReconciliationService::class)->anomalies();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertSame(1, $anomalies['calls_missing_wallet_transaction']);
        $this->assertSame(1, $anomalies['calls_missing_earning_ledger']);
        $this->assertSame(1, $anomalies['payment_success_without_wallet_transaction']);
        $this->assertSame(1, $anomalies['mismatched_recharge_coin_amount']);
        $this->assertLessThanOrEqual(15, $queryCount);
    }

    private function endedBilledCall(User $caller, User $hostUser, Host $host, Agency $agency): CallSession
    {
        return CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $hostUser->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'type' => 'video',
            'status' => 'ended',
            'coin_rate_per_minute' => 20,
            'billable_minutes' => 1,
            'total_coins_charged' => 20,
            'host_earning' => 12,
            'agency_earning' => 2,
            'platform_earning' => 6,
            'billing_processed_at' => now(),
        ]);
    }

    private function insertWalletTransaction(Wallet $wallet, array $attributes): void
    {
        DB::table('wallet_transactions')->insert(array_merge([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'coins' => 0,
            'amount' => null,
            'currency' => null,
            'category' => 'adjustment',
            'reference' => null,
            'transaction_id' => null,
            'gateway' => null,
            'counterparty_user_id' => null,
            'meta' => null,
            'reference_type' => null,
            'reference_id' => null,
            'description' => null,
            'balance_before' => null,
            'balance_after' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }
}
