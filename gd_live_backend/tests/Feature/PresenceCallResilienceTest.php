<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\HostAvailability;
use App\Models\User;
use App\Services\HostAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresenceCallResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_online_heartbeat_prevents_an_active_call_from_being_cleaned_up_as_stale(): void
    {
        $caller = User::factory()->create();
        $receiver = User::factory()->create();

        $call = CallSession::query()->create([
            'caller_id' => $caller->id,
            'receiver_id' => $receiver->id,
            'type' => 'video',
            'status' => 'accepted',
            'livekit_room_name' => 'call_presence_test',
            'started_at' => now()->subMinute(),
            'accepted_at' => now()->subMinute(),
            'coin_rate_per_minute' => 20,
        ]);

        HostAvailability::query()->create([
            'user_id' => $receiver->id,
            'manual_status' => 'online',
            'socket_status' => 'online',
            'call_status' => 'busy',
            'current_call_session_id' => $call->id,
            'last_seen_at' => now()->subMinutes(5),
        ]);

        $service = app(HostAvailabilityService::class);
        $service->updateSocketStatus($receiver->id, 'online');

        $this->assertSame(0, $service->cleanupStaleSocketStatuses(120));
        $this->assertDatabaseHas('call_sessions', [
            'id' => $call->id,
            'status' => 'accepted',
            'end_reason' => null,
        ]);
    }
}
