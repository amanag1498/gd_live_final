<?php

namespace Tests\Feature;

use App\Models\DevicePushToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PushTokenRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ios_token_registration_replaces_the_previous_token_for_the_device(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        DevicePushToken::query()->create([
            'user_id' => $user->id,
            'device_id' => 'ios:device-one',
            'platform' => 'ios',
            'token' => 'old-token',
            'last_seen_at' => now()->subDay(),
        ]);

        $this->withHeaders([
            'X-Device-Id' => 'ios:device-one',
            'X-Client-Platform' => 'ios',
            'X-App-Version-Code' => '63',
        ])->postJson('/api/push/register', [
            'token' => 'new-token',
            'platform' => 'ios',
        ])->assertOk();

        $this->assertDatabaseMissing('device_push_tokens', [
            'token' => 'old-token',
        ]);
        $this->assertDatabaseHas('device_push_tokens', [
            'user_id' => $user->id,
            'device_id' => 'ios:device-one',
            'platform' => 'ios',
            'token' => 'new-token',
        ]);
    }

    public function test_push_registration_rejects_unknown_platforms(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/push/register', [
            'token' => 'token',
            'platform' => 'desktop',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('platform');
    }

    public function test_legacy_push_registration_defaults_to_android(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->withHeaders([
            'X-Device-Id' => 'legacy-android-device',
        ])->postJson('/api/push/register', [
            'token' => 'legacy-token',
        ])->assertOk();

        $this->assertDatabaseHas('device_push_tokens', [
            'user_id' => $user->id,
            'device_id' => 'legacy-android-device',
            'platform' => 'android',
            'token' => 'legacy-token',
        ]);
    }
}
