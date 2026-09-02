<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\CallEarningLedger;
use App\Models\CallSession;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgencyReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_agency_backfill_command_fills_missing_agency_ids(): void
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);

        $hostUser = User::factory()->create();
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
            'agency_id' => null,
            'type' => 'video',
            'status' => 'ended',
            'coin_rate_per_minute' => 20,
            'billable_minutes' => 1,
            'total_coins_charged' => 20,
            'host_earning' => 12,
            'agency_earning' => 2,
            'platform_earning' => 6,
        ]);

        CallEarningLedger::query()->create([
            'call_session_id' => $call->id,
            'caller_id' => $caller->id,
            'host_id' => $host->id,
            'agency_id' => null,
            'total_coins' => 20,
            'host_earning' => 12,
            'agency_earning' => 2,
            'platform_earning' => 6,
            'duration_seconds' => 60,
            'billable_minutes' => 1,
        ]);

        Artisan::call('agency:backfill');

        $this->assertDatabaseHas('call_sessions', [
            'id' => $call->id,
            'agency_id' => $agency->id,
        ]);

        $this->assertDatabaseHas('call_earning_ledgers', [
            'call_session_id' => $call->id,
            'agency_id' => $agency->id,
        ]);
    }

    public function test_admin_can_view_agency_reports_and_detail(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $owner = User::factory()->create(['name' => 'Agency Owner']);
        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);

        $hostUser = User::factory()->create(['name' => 'Host Nova']);
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'Nova',
        ]);

        $caller = User::factory()->create(['name' => 'Caller One']);

        CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $hostUser->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'type' => 'video',
            'status' => 'ended',
            'coin_rate_per_minute' => 45,
            'billable_minutes' => 2,
            'total_coins_charged' => 90,
            'host_earning' => 54,
            'agency_earning' => 9,
            'platform_earning' => 27,
        ]);

        LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'room-orbit-1',
            'title' => 'Orbit Live',
            'status' => 'ended',
            'started_at' => now()->subMinutes(20),
            'ended_at' => now()->subMinutes(5),
            'last_activity_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.agencies'))
            ->assertOk()
            ->assertSee('Agency Reports')
            ->assertSee('Orbit Agency')
            ->assertSee('Live Rooms');

        $this->actingAs($admin)
            ->get(route('admin.reports.agencies.show', $agency))
            ->assertOk()
            ->assertSee('Orbit Agency')
            ->assertSee('Host Nova')
            ->assertSee('User #'.$hostUser->id);

        $this->actingAs($admin)
            ->get(route('admin.calls.index'))
            ->assertOk()
            ->assertSee('User ID: '.$hostUser->id);

        $csv = $this->actingAs($admin)->get(route('admin.calls.export'))->streamedContent();
        $this->assertStringContainsString('host_user_id', $csv);
        $this->assertStringContainsString((string) $hostUser->id, $csv);

        Sanctum::actingAs($admin);
        $this->getJson('/api/admin/calls')
            ->assertOk()
            ->assertJsonPath('data.items.0.host_user_id', $hostUser->id);

        $apiCsv = $this->get('/api/admin/calls/export')->streamedContent();
        $this->assertStringContainsString('host_user_id', $apiCsv);
        $this->assertStringNotContainsString(',host_id,', $apiCsv);

        $this->actingAs($admin)
            ->get(route('admin.calls.index'))
            ->assertOk()
            ->assertSee('User ID: '.$hostUser->id);
    }

    public function test_admin_can_view_host_report_detail(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);

        $hostUser = User::factory()->create(['name' => 'Host Nova']);
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'Nova',
        ]);

        $caller = User::factory()->create(['name' => 'Caller One']);

        CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $hostUser->id,
            'host_id' => $host->id,
            'agency_id' => $agency->id,
            'type' => 'video',
            'status' => 'ended',
            'coin_rate_per_minute' => 20,
            'billable_minutes' => 3,
            'total_coins_charged' => 60,
            'host_earning' => 36,
            'agency_earning' => 6,
            'platform_earning' => 18,
        ]);

        LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'room-host-1',
            'title' => 'Nova Live',
            'status' => 'ended',
            'started_at' => now()->subMinutes(18),
            'ended_at' => now()->subMinutes(3),
            'last_activity_at' => now()->subMinutes(3),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.hosts.show', $host))
            ->assertOk()
            ->assertSee('Host Nova')
            ->assertSee('User ID: '.$hostUser->id)
            ->assertSee('Recent Calls')
            ->assertSee('Recent Live Rooms');
    }

    public function test_agency_host_reports_are_limited_to_its_own_hosts(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('agency');
        $agency = Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);
        $ownHostUser = User::factory()->create(['name' => 'Orbit Host']);
        $ownHost = Host::query()->create([
            'user_id' => $ownHostUser->id,
            'agency_id' => $agency->id,
            'stage_name' => 'Orbit Nova',
        ]);

        $otherOwner = User::factory()->create();
        $otherAgency = Agency::query()->create([
            'owner_user_id' => $otherOwner->id,
            'name' => 'Other Agency',
        ]);
        $otherHostUser = User::factory()->create(['name' => 'Other Host']);
        $otherHost = Host::query()->create([
            'user_id' => $otherHostUser->id,
            'agency_id' => $otherAgency->id,
            'stage_name' => 'Other Nova',
        ]);

        $this->actingAs($owner)
            ->get(route('agency.reports.hosts', ['range' => 'weekly']))
            ->assertOk()
            ->assertSee('Host Reports')
            ->assertSee('Orbit Host')
            ->assertDontSee('Other Host');

        $csv = $this->actingAs($owner)
            ->get(route('agency.reports.hosts.csv', ['range' => 'weekly']))
            ->assertOk()
            ->streamedContent();
        $exportedHostUserIds = collect(preg_split('/\r\n|\r|\n/', trim($csv)))
            ->skip(1)
            ->filter()
            ->map(fn (string $line) => str_getcsv($line)[1] ?? null)
            ->filter()
            ->map(fn (string $id) => (int) $id)
            ->values();
        $this->assertContains($ownHostUser->id, $exportedHostUserIds);
        $this->assertNotContains($otherHostUser->id, $exportedHostUserIds);

        $this->actingAs($owner)
            ->get(route('agency.reports.hosts.show', $ownHost))
            ->assertOk()
            ->assertSee('Orbit Host')
            ->assertSee('Weekly Breakdown');

        $this->actingAs($owner)
            ->get(route('agency.reports.hosts.show', $otherHost))
            ->assertNotFound();

        $this->actingAs($owner)
            ->get(route('agency.reports.hosts', ['host_id' => $otherHost->id]))
            ->assertNotFound();
    }

    public function test_agency_login_destination_renders_for_ready_and_pending_agencies(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('agency');
        Agency::query()->create([
            'owner_user_id' => $owner->id,
            'name' => 'Orbit Agency',
        ]);

        $this->actingAs($owner)
            ->get(route('agency.dashboard'))
            ->assertOk()
            ->assertSee('Agency Dashboard')
            ->assertSee('Host Reports')
            ->assertSee('Orbit Agency');

        $pendingOwner = User::factory()->create();
        $pendingOwner->assignRole('agency');

        $this->actingAs($pendingOwner)
            ->get(route('agency.dashboard'))
            ->assertOk()
            ->assertSee('Agency not ready');
    }

    public function test_avatar_media_route_serves_public_avatar_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/7/test.jpg', 'fake-image');

        $owner = User::factory()->create([
            'avatar_url' => 'avatars/7/test.jpg',
        ]);

        $response = $this->get(route('media.avatar', ['path' => 'avatars/7/test.jpg']));

        $response->assertOk();
        $this->assertStringContainsString('/media/avatar/avatars/7/test.jpg', $owner->avatar_url);
    }
}
