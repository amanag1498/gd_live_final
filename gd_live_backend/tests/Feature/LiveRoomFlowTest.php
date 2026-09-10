<?php

namespace Tests\Feature;

use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomParticipant;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\LiveKitRoomAdminService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LiveRoomFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        Redis::shouldReceive('publish', 'set', 'del', 'sadd', 'srem')
            ->zeroOrMoreTimes()
            ->andReturn(1)
            ->byDefault();
        Redis::shouldReceive('smembers')
            ->zeroOrMoreTimes()
            ->andReturn([])
            ->byDefault();
    }

    public function test_live_room_list_returns_db_backed_counts(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        $viewer->assignRole('user');

        LiveRoomParticipant::query()->create([
            'live_room_id' => $room->id,
            'user_id' => $viewer->id,
            'role' => 'viewer',
            'joined_at' => now(),
        ]);

        Sanctum::actingAs($viewer);

        $response = $this->getJson('/api/live/rooms');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.room_id', $room->room_id)
            ->assertJsonPath('data.0.viewer_count', 1)
            ->assertJsonPath('data.0.participant_count', 2)
            ->assertJsonPath('data.0.host_id', $hostUser->id);
    }

    public function test_duplicate_join_reuses_existing_active_participant(): void
    {
        [, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        $viewer->assignRole('user');
        $this->grantSubscription($viewer);
        Sanctum::actingAs($viewer);

        $first = $this->postJson("/api/live/rooms/{$room->room_id}/join", [
            'role' => 'viewer',
            'session_id' => 'sess-a',
        ])->assertOk();

        $second = $this->postJson("/api/live/rooms/{$room->room_id}/join", [
            'role' => 'viewer',
            'session_id' => 'sess-a',
        ])->assertOk();

        $this->assertSame(
            data_get($first->json(), 'participant_id'),
            data_get($second->json(), 'participant_id')
        );

        $this->assertSame(1, LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->where('user_id', $viewer->id)
            ->whereNull('left_at')
            ->count());
    }

    public function test_viewer_join_does_not_refresh_host_liveness(): void
    {
        [, $room] = $this->makeLiveRoom();
        $room->forceFill(['last_activity_at' => now()->subMinutes(10)])->save();
        $lastHostActivity = $room->last_activity_at;

        $viewer = User::factory()->create();
        $viewer->assignRole('user');
        $this->grantSubscription($viewer);
        Sanctum::actingAs($viewer);

        $this->postJson("/api/live/rooms/{$room->room_id}/join", [
            'role' => 'viewer',
            'session_id' => 'viewer-liveness-test',
        ])->assertOk();

        $this->assertSame(
            $lastHostActivity->toDateTimeString(),
            $room->fresh()->last_activity_at->toDateTimeString(),
            'Viewer traffic must not keep a disconnected host room alive.',
        );
    }

    public function test_join_missing_room_returns_clean_room_not_found_error(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('user');
        $this->grantSubscription($viewer);
        Sanctum::actingAs($viewer);

        $this->postJson('/api/live/rooms/missing-room/join', [
            'role' => 'viewer',
            'session_id' => 'sess-a',
        ])->assertStatus(404)
            ->assertJsonPath('message', 'room_not_found');
    }

    public function test_join_ended_room_returns_clean_room_not_joinable_error(): void
    {
        [, $room] = $this->makeLiveRoom(status: 'ended');
        $viewer = User::factory()->create();
        $viewer->assignRole('user');
        $this->grantSubscription($viewer);
        Sanctum::actingAs($viewer);

        $this->postJson("/api/live/rooms/{$room->room_id}/join", [
            'role' => 'viewer',
            'session_id' => 'sess-a',
        ])->assertStatus(409)
            ->assertJsonPath('message', 'room_not_joinable');
    }

    public function test_leave_is_idempotent_and_closes_all_duplicate_open_rows(): void
    {
        [, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        $viewer->assignRole('user');

        foreach ([1, 2] as $offset) {
            LiveRoomParticipant::query()->create([
                'live_room_id' => $room->id,
                'user_id' => $viewer->id,
                'role' => 'viewer',
                'joined_at' => now()->subMinutes($offset),
            ]);
        }

        Sanctum::actingAs($viewer);

        $this->postJson("/api/live/rooms/{$room->room_id}/leave", [
            'session_id' => 'sess-a',
        ])->assertOk();

        $this->postJson("/api/live/rooms/{$room->room_id}/leave", [
            'session_id' => 'sess-a',
        ])->assertOk();

        $this->assertSame(0, LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->where('user_id', $viewer->id)
            ->whereNull('left_at')
            ->count());
    }

    public function test_host_end_flow_is_idempotent_and_closes_open_participants(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        $viewer->assignRole('user');

        LiveRoomParticipant::query()->create([
            'live_room_id' => $room->id,
            'user_id' => $viewer->id,
            'role' => 'viewer',
            'joined_at' => now()->subMinute(),
        ]);

        Sanctum::actingAs($hostUser);

        $this->postJson("/api/live/rooms/{$room->room_id}/end")->assertOk();
        $this->postJson("/api/live/rooms/{$room->room_id}/end")->assertOk();

        $this->assertDatabaseHas('live_rooms', [
            'id' => $room->id,
            'status' => 'ended',
            'end_reason' => 'host_ended',
        ]);
        $this->assertSame(0, LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->whereNull('left_at')
            ->count());
    }

    public function test_admin_force_end_records_the_action_time(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();
        $forceEndedAt = now()->addMinute()->startOfSecond();

        $this->travelTo($forceEndedAt);

        $this->actingAs($admin)
            ->from(route('admin.live-rooms.show', $room))
            ->post(route('admin.live-rooms.end', $room))
            ->assertRedirect(route('admin.live-rooms.show', $room));

        $room->refresh();

        $this->assertSame('ended', $room->status);
        $this->assertSame('admin_force_end', $room->end_reason);
        $this->assertSame($forceEndedAt->toDateTimeString(), $room->ended_at?->toDateTimeString());
        $this->assertSame($forceEndedAt->toDateTimeString(), $room->last_activity_at?->toDateTimeString());
        $this->assertSame(0, LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->whereNull('left_at')
            ->count());
    }

    public function test_admin_force_end_fills_a_missing_end_time_on_an_ended_room(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();
        $room->forceFill([
            'status' => 'ended',
            'ended_at' => null,
            'end_reason' => null,
        ])->save();
        $forceEndedAt = now()->addMinute()->startOfSecond();

        $this->travelTo($forceEndedAt);

        $this->actingAs($admin)
            ->post(route('admin.live-rooms.end', $room))
            ->assertRedirect();

        $room->refresh();

        $this->assertSame('ended', $room->status);
        $this->assertSame('admin_force_end', $room->end_reason);
        $this->assertSame($forceEndedAt->toDateTimeString(), $room->ended_at?->toDateTimeString());
    }

    public function test_admin_can_open_silent_live_room_observer_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();

        $this->actingAs($admin)
            ->get(route('admin.live-rooms.watch', $room))
            ->assertOk()
            ->assertSee('Start silent watch')
            ->assertSee('observer-token');
    }

    public function test_admin_live_room_filters_apply_status_dates_and_grouped_search(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        [, $matchingRoom] = $this->makeLiveRoom();
        $matchingRoom->forceFill([
            'room_id' => 'filter-live-match',
            'title' => 'Operations Match',
            'created_at' => '2026-09-02 10:00:00',
            'updated_at' => '2026-09-02 10:00:00',
        ])->save();

        [, $endedRoom] = $this->makeLiveRoom(status: 'ended');
        $endedRoom->forceFill([
            'room_id' => 'filter-ended-room',
            'title' => 'Operations Match',
            'created_at' => '2026-09-02 11:00:00',
            'updated_at' => '2026-09-02 11:00:00',
        ])->save();

        [, $outsideDateRoom] = $this->makeLiveRoom();
        $outsideDateRoom->forceFill([
            'room_id' => 'filter-outside-date',
            'title' => 'Operations Match',
            'created_at' => '2026-08-20 10:00:00',
            'updated_at' => '2026-08-20 10:00:00',
        ])->save();

        [, $nonMatchingRoom] = $this->makeLiveRoom();
        $nonMatchingRoom->forceFill([
            'room_id' => 'filter-non-matching',
            'title' => 'Different Room',
            'created_at' => '2026-09-02 12:00:00',
            'updated_at' => '2026-09-02 12:00:00',
        ])->save();

        $this->actingAs($admin)
            ->get(route('admin.live-rooms.index', [
                's' => 'Operations',
                'status' => 'live',
                'from' => '2026-09-01',
                'to' => '2026-09-03',
            ]))
            ->assertOk()
            ->assertSee('filter-live-match')
            ->assertDontSee('filter-ended-room')
            ->assertDontSee('filter-outside-date')
            ->assertDontSee('filter-non-matching');

        $this->actingAs($admin)
            ->get(route('admin.live-rooms.index', ['status' => 'ended']))
            ->assertOk()
            ->assertSee('filter-ended-room')
            ->assertDontSee('filter-live-match');
    }

    public function test_admin_observer_token_is_hidden_subscribe_only_and_does_not_create_participant(): void
    {
        config([
            'app.url' => 'https://admin.example.test',
            'services.livekit.api_key' => 'test-livekit-key',
            'services.livekit.api_secret' => 'test-livekit-secret',
            'services.livekit.ws_url' => 'wss://livekit.example.test',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();
        $roomStateBefore = $room->fresh()->getRawOriginal();
        $beforeParticipants = LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->count();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.live-rooms.observer-token', $room))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('room', $room->room_id)
            ->assertJsonPath('ws_url', 'wss://livekit.example.test')
            ->assertHeader('Cache-Control', 'no-store, private');

        $payload = $this->decodeJwtPayload($response->json('token'));

        $this->assertStringStartsWith('admin-observer:'.$admin->id.':', $payload['sub']);
        $this->assertSame($room->room_id, $payload['video']['room']);
        $this->assertTrue($payload['video']['roomJoin']);
        $this->assertTrue($payload['video']['canSubscribe']);
        $this->assertFalse($payload['video']['canPublish']);
        $this->assertFalse($payload['video']['canPublishData']);
        $this->assertFalse($payload['video']['canUpdateOwnMetadata']);
        $this->assertTrue($payload['video']['hidden']);
        $this->assertArrayNotHasKey('roomCreate', $payload['video']);
        $this->assertArrayNotHasKey('roomAdmin', $payload['video']);

        $this->assertSame($beforeParticipants, LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->count());
        $this->assertSame($roomStateBefore, $room->fresh()->getRawOriginal());

        $this->assertDatabaseHas('live_room_admin_audits', [
            'live_room_id' => $room->id,
            'admin_id' => $admin->id,
            'action' => 'admin_observer_token_issued',
            'reason' => 'silent_watch',
        ]);
    }

    public function test_admin_observer_token_fails_closed_when_livekit_is_not_configured(): void
    {
        config([
            'services.livekit.api_key' => '',
            'services.livekit.api_secret' => '',
            'services.livekit.ws_url' => '',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();

        $this->actingAs($admin)
            ->postJson(route('admin.live-rooms.observer-token', $room))
            ->assertStatus(503)
            ->assertJsonPath('ok', false);

        $this->assertDatabaseMissing('live_room_admin_audits', [
            'live_room_id' => $room->id,
            'action' => 'admin_observer_token_issued',
        ]);
    }

    public function test_admin_observer_requires_secure_livekit_url_on_https_admin_portal(): void
    {
        config([
            'app.url' => 'https://admin.example.test',
            'services.livekit.api_key' => 'test-livekit-key',
            'services.livekit.api_secret' => 'test-livekit-secret',
            'services.livekit.ws_url' => 'ws://livekit.example.test',
            'services.livekit.browser_ws_url' => null,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom();

        $this->actingAs($admin);
        $response = $this->call(
            'POST',
            route('admin.live-rooms.observer-token', $room, absolute: false),
            server: ['HTTPS' => 'on', 'HTTP_ACCEPT' => 'application/json'],
        );

        $response
            ->assertStatus(503)
            ->assertJsonPath(
                'message',
                'Silent watch requires LIVEKIT_BROWSER_WS_URL to use wss:// on this HTTPS admin portal.'
            );
    }

    public function test_admin_observer_token_rejects_ended_room(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        [, $room] = $this->makeLiveRoom(status: 'ended');

        $this->actingAs($admin)
            ->postJson(route('admin.live-rooms.observer-token', $room))
            ->assertStatus(409)
            ->assertJsonPath('ok', false);
    }

    public function test_cleanup_command_ends_live_room_without_active_host(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();

        LiveRoomParticipant::query()
            ->where('live_room_id', $room->id)
            ->where('user_id', $hostUser->id)
            ->where('role', 'host')
            ->update([
                'left_at' => now()->subMinutes(3),
                'duration_seconds' => 120,
            ]);

        Artisan::call('live-rooms:cleanup', ['--stale-minutes' => 15]);

        $this->assertDatabaseHas('live_rooms', [
            'id' => $room->id,
            'status' => 'ended',
            'end_reason' => 'host_left',
        ]);
    }

    public function test_cleanup_ends_fresh_database_room_when_livekit_host_is_missing(): void
    {
        [, $room] = $this->makeLiveRoom();
        $room->forceFill(['last_activity_at' => now()])->save();

        $livekit = $this->mock(LiveKitRoomAdminService::class);
        $livekit->shouldReceive('hasActiveHost')
            ->once()
            ->with($room->room_id)
            ->andReturn(false);

        Artisan::call('live-rooms:cleanup', ['--stale-minutes' => 2]);

        $this->assertDatabaseHas('live_rooms', [
            'id' => $room->id,
            'status' => 'ended',
            'end_reason' => 'host_disconnected',
        ]);
    }

    public function test_cleanup_preserves_room_with_active_livekit_host(): void
    {
        [, $room] = $this->makeLiveRoom();
        $room->forceFill(['last_activity_at' => now()])->save();

        $livekit = $this->mock(LiveKitRoomAdminService::class);
        $livekit->shouldReceive('hasActiveHost')
            ->once()
            ->with($room->room_id)
            ->andReturn(true);

        Artisan::call('live-rooms:cleanup', ['--stale-minutes' => 2]);

        $this->assertDatabaseHas('live_rooms', [
            'id' => $room->id,
            'status' => 'live',
            'ended_at' => null,
        ]);
    }

    public function test_sync_redis_command_rebuilds_only_live_room_docs(): void
    {
        [, $liveRoom] = $this->makeLiveRoom();
        [, $endedRoom] = $this->makeLiveRoom(status: 'ended');

        Redis::shouldReceive('del')->once()->with('rooms:live')->andReturn(1);
        Redis::shouldReceive('sadd')->once()->with('rooms:live', $liveRoom->room_id)->andReturn(1);
        Redis::shouldReceive('set')->once()->withArgs(function ($key, $value) use ($liveRoom) {
            return $key === "rooms:room:{$liveRoom->room_id}" && str_contains($value, $liveRoom->room_id);
        })->andReturn(true);

        Artisan::call('live-rooms:sync-redis');

        $this->assertStringContainsString('Synced 1 live room(s) to Redis.', Artisan::output());
        $this->assertSame('ended', $endedRoom->status);
    }

    public function test_reconcile_command_detects_live_room_without_host_duplicate_participants_and_redis_mismatch(): void
    {
        [, $liveRoom] = $this->makeLiveRoom();
        LiveRoomParticipant::query()
            ->where('live_room_id', $liveRoom->id)
            ->where('role', 'host')
            ->delete();

        $viewer = User::factory()->create();
        foreach ([1, 2] as $offset) {
            LiveRoomParticipant::query()->create([
                'live_room_id' => $liveRoom->id,
                'user_id' => $viewer->id,
                'role' => 'viewer',
                'joined_at' => now()->subMinutes($offset),
            ]);
        }

        Redis::shouldReceive('smembers')->once()->with('rooms:live')->andReturn([]);

        $this->artisan('live-rooms:reconcile')
            ->expectsOutputToContain('live_room_without_host')
            ->expectsOutputToContain('duplicate_open_participants')
            ->assertExitCode(0);
    }

    private function makeLiveRoom(string $status = 'live'): array
    {
        $hostUser = User::factory()->create();
        $hostUser->assignRole('host');

        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'stage_name' => 'Room Host',
        ]);

        $room = LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'room-'.$host->id.'-'.$status,
            'title' => 'Live Room',
            'status' => $status,
            'started_at' => $status === 'live' ? now()->subMinutes(5) : now()->subMinutes(10),
            'ended_at' => $status === 'ended' ? now()->subMinute() : null,
            'end_reason' => $status === 'ended' ? 'host_ended' : null,
            'last_activity_at' => now()->subMinute(),
            'peak_viewers' => 3,
        ]);

        if ($status === 'live') {
            LiveRoomParticipant::query()->create([
                'live_room_id' => $room->id,
                'user_id' => $hostUser->id,
                'role' => 'host',
                'joined_at' => now()->subMinutes(5),
            ]);
        }

        return [$hostUser, $room];
    }

    private function grantSubscription(User $user): void
    {
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Viewer Pass '.$user->id,
            'price_coins' => 100,
            'duration_days' => 30,
            'perks' => ['live_access' => true],
            'is_active' => true,
        ]);

        UserSubscription::query()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addDays(10),
            'last_purchased_at' => now()->subMinute(),
            'meta' => ['source' => 'test'],
        ]);
    }

    private function decodeJwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);

        $payload = strtr($parts[1], '-_', '+/');
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);

        return json_decode(base64_decode($payload), true, flags: JSON_THROW_ON_ERROR);
    }
}
