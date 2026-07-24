<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AppSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppFeatureFlagEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->member = User::factory()->create();
        $this->member->assignRole('host');

        config([
            'app_features.maintenance_mode_enabled' => false,
            'app_features.force_app_upgrade_enabled' => false,
            'app_features.platform.android.video_rooms_enabled' => true,
            'app_features.platform.android.pk_battles_enabled' => true,
            'app_features.platform.android.gifts_enabled' => true,
            'app_features.platform.android.subscriptions_enabled' => true,
            'app_features.platform.android.entry_effects_enabled' => true,
            'app_features.platform.android.wallet_recharge_enabled' => true,
            'app_features.platform.android.host_calling_enabled' => true,
            'app_features.platform.ios.video_rooms_enabled' => true,
            'app_features.platform.ios.pk_battles_enabled' => true,
            'app_features.platform.ios.gifts_enabled' => true,
            'app_features.platform.ios.subscriptions_enabled' => true,
            'app_features.platform.ios.entry_effects_enabled' => true,
            'app_features.platform.ios.wallet_recharge_enabled' => true,
            'app_features.platform.ios.host_calling_enabled' => true,
        ]);
    }

    public function test_non_admin_cannot_access_app_settings_dashboard(): void
    {
        $this->actingAs($this->member)
            ->get(route('admin.settings.app.edit'))
            ->assertForbidden();
    }

    public function test_maintenance_mode_blocks_member_apis_but_keeps_admin_web_and_app_config_available(): void
    {
        config(['app_features.maintenance_mode_enabled' => true]);

        Sanctum::actingAs($this->member);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/profile')
            ->assertStatus(503)
            ->assertJsonPath('error', 'MAINTENANCE_MODE');

        $this->getJson('/api/app-config')
            ->assertOk()
            ->assertJsonPath('data.maintenance_mode_enabled', true);

        $this->actingAs($this->admin)
            ->get(route('admin.settings.app.edit'))
            ->assertOk();
    }

    public function test_force_upgrade_rejects_old_android_client_headers(): void
    {
        config([
            'app_features.force_app_upgrade_enabled' => true,
            'app_features.android_min_version_code' => 5,
        ]);

        Sanctum::actingAs($this->member);

        $this->withHeaders($this->androidHeaders(versionCode: 4))
            ->getJson('/api/profile')
            ->assertStatus(426)
            ->assertJsonPath('error', 'APP_UPGRADE_REQUIRED');

        $this->withHeaders($this->androidHeaders(versionCode: 5))
            ->getJson('/api/profile')
            ->assertOk();
    }

    public function test_force_upgrade_uses_the_ios_build_number(): void
    {
        config([
            'app_features.force_app_upgrade_enabled' => true,
            'app_features.ios_min_version_code' => 7,
        ]);

        Sanctum::actingAs($this->member);

        $this->withHeaders($this->iosHeaders(versionCode: 6))
            ->getJson('/api/profile')
            ->assertStatus(426)
            ->assertJsonPath('error', 'APP_UPGRADE_REQUIRED')
            ->assertJsonPath('platform', 'ios')
            ->assertJsonPath('minimum_ios_version_code', 7);

        $this->withHeaders($this->iosHeaders(versionCode: 7))
            ->getJson('/api/profile')
            ->assertOk();
    }

    public function test_trusted_realtime_server_requests_bypass_client_version_checks(): void
    {
        config([
            'app_features.force_app_upgrade_enabled' => true,
            'app_features.android_min_version_code' => 99,
        ]);
        putenv('WS_INTERNAL_KEY=test-internal-key');

        try {
            Sanctum::actingAs($this->member);

            $this->withHeaders([
                'X-WS-Internal-Key' => 'test-internal-key',
            ])->getJson('/api/profile')
                ->assertOk();
        } finally {
            putenv('WS_INTERNAL_KEY');
        }
    }

    public function test_ios_feature_flags_do_not_change_android_flags(): void
    {
        Sanctum::actingAs($this->member);

        config([
            'app_features.platform.android.gifts_enabled' => true,
            'app_features.platform.ios.gifts_enabled' => false,
        ]);

        $this->withHeaders($this->iosHeaders())
            ->getJson('/api/gifts')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'gifts_enabled');

        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/gifts')
            ->assertOk();
    }

    public function test_ios_wallet_recharge_uses_its_platform_kill_switch(): void
    {
        Sanctum::actingAs($this->member);

        config(['app_features.platform.ios.wallet_recharge_enabled' => false]);
        $this->withHeaders($this->iosHeaders())
            ->getJson('/api/recharge/plans')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'wallet_recharge_enabled');

        config(['app_features.platform.ios.wallet_recharge_enabled' => true]);
        $this->withHeaders($this->iosHeaders())
            ->getJson('/api/recharge/plans')
            ->assertOk();
    }

    public function test_missing_platform_header_uses_android_feature_flags(): void
    {
        Sanctum::actingAs($this->member);
        config([
            'app_features.platform.android.gifts_enabled' => false,
            'app_features.platform.ios.gifts_enabled' => true,
        ]);

        $this->getJson('/api/gifts')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'gifts_enabled');
    }

    public function test_disabled_feature_apis_are_rejected(): void
    {
        Sanctum::actingAs($this->member);

        config(['app_features.platform.android.video_rooms_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->postJson('/api/live/rooms', ['title' => 'Video'])
            ->assertStatus(403)
            ->assertJsonPath('feature', 'video_rooms_enabled');

        config(['app_features.platform.android.gifts_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/gifts')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'gifts_enabled');

        config(['app_features.platform.android.subscriptions_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/plans')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'subscriptions_enabled');

        config(['app_features.platform.android.entry_effects_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/entry-packs')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'entry_effects_enabled');

        config(['app_features.platform.android.wallet_recharge_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/recharge/plans')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'wallet_recharge_enabled');

        config(['app_features.platform.android.host_calling_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/calls/history')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'host_calling_enabled');

        config(['app_features.platform.android.pk_battles_enabled' => false]);
        $this->withHeaders($this->androidHeaders())
            ->getJson('/api/live/rooms/example-room/pk/active')
            ->assertStatus(403)
            ->assertJsonPath('feature', 'pk_battles_enabled');
    }

    public function test_invalid_app_settings_values_are_rejected(): void
    {
        $payload = $this->validAppSettingsPayload();

        $this->actingAs($this->admin)
            ->put(route('admin.settings.app.update'), $payload)
            ->assertSessionHasNoErrors();
    }

    private function androidHeaders(int $versionCode = 10): array
    {
        return [
            'X-Client-Platform' => 'android',
            'X-App-Version' => '1.0.0',
            'X-App-Version-Code' => (string) $versionCode,
        ];
    }

    private function iosHeaders(int $versionCode = 10): array
    {
        return [
            'X-Client-Platform' => 'ios',
            'X-App-Version' => '1.0.0',
            'X-App-Version-Code' => (string) $versionCode,
        ];
    }

    private function validAppSettingsPayload(): array
    {
        $payload = [];
        foreach (AppSettingsService::APP_DEFINITIONS as $key => $definition) {
            data_set($payload, $key, $definition['default']);
        }

        return $payload;
    }
}
