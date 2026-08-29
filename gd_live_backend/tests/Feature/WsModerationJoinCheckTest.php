<?php

namespace Tests\Feature;

use App\Models\Host;
use App\Models\HostUserBlock;
use App\Models\LiveRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WsModerationJoinCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_host_is_never_blocked_from_own_room(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();
        Sanctum::actingAs($hostUser);

        $this->postJson('/api/ws/rooms/join-check', [
            'room_id' => $room->room_id,
        ])->assertOk()
            ->assertJsonPath('allow', true)
            ->assertJsonPath('code', null)
            ->assertJsonPath('host_user_id', $hostUser->id)
            ->assertJsonPath('target_user_id', $hostUser->id);
    }

    public function test_unblocked_viewer_receives_an_explicit_allow_decision(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        Sanctum::actingAs($viewer);

        $this->postJson('/api/ws/rooms/join-check', [
            'room_id' => $room->room_id,
        ])->assertOk()
            ->assertJsonPath('allow', true)
            ->assertJsonPath('code', null)
            ->assertJsonPath('host_user_id', $hostUser->id)
            ->assertJsonPath('target_user_id', $viewer->id);
    }

    public function test_only_a_persisted_host_user_block_returns_host_blocked(): void
    {
        [$hostUser, $room] = $this->makeLiveRoom();
        $viewer = User::factory()->create();
        HostUserBlock::query()->create([
            'host_user_id' => $hostUser->id,
            'blocked_user_id' => $viewer->id,
            'blocked_by_user_id' => $hostUser->id,
            'blocked_by_role' => 'host',
        ]);
        Sanctum::actingAs($viewer);

        $this->postJson('/api/ws/rooms/join-check', [
            'room_id' => $room->room_id,
        ])->assertOk()
            ->assertJsonPath('allow', false)
            ->assertJsonPath('code', 'HOST_BLOCKED')
            ->assertJsonPath('host_user_id', $hostUser->id)
            ->assertJsonPath('target_user_id', $viewer->id);
    }

    private function makeLiveRoom(): array
    {
        $hostUser = User::factory()->create();
        $host = Host::query()->create([
            'user_id' => $hostUser->id,
            'stage_name' => 'Moderation Host',
        ]);
        $room = LiveRoom::query()->create([
            'host_id' => $host->id,
            'room_id' => 'moderation-room-'.$host->id,
            'title' => 'Moderation Room',
            'room_type' => 'video',
            'status' => 'live',
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);

        return [$hostUser, $room];
    }
}
