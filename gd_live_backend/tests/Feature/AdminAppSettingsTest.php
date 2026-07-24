<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use App\Services\AppSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAppSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_admin_can_view_app_settings_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.settings.app.edit'))
            ->assertOk()
            ->assertSee('App Settings')
            ->assertSee('Maintenance Mode')
            ->assertSee('Android Feature Flags')
            ->assertSee('iOS Feature Flags');
    }

    public function test_admin_can_update_app_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = $this->validAppSettingsPayload([
            'app_features.force_app_upgrade_enabled' => 1,
            'app_features.platform.android.pk_battles_enabled' => 0,
            'app_features.platform.android.host_calling_enabled' => 0,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.app.update'), $payload)
            ->assertRedirect(route('admin.settings.app.edit'));

        $this->assertDatabaseHas('app_settings', [
            'key' => 'app_features.force_app_upgrade_enabled',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('app_settings', [
            'key' => 'app_features.platform.android.pk_battles_enabled',
            'value' => '0',
        ]);
    }

    public function test_app_settings_are_loaded_into_config_from_database(): void
    {
        AppSetting::query()->create([
            'key' => 'app_features.maintenance_mode_enabled',
            'value' => '1',
        ]);
        app(AppSettingsService::class)->loadAppSettingsIntoConfig();

        $this->assertTrue(config('app_features.maintenance_mode_enabled'));
        $this->assertTrue(config('app_features.platform.android.video_rooms_enabled'));
    }

    public function test_public_app_settings_endpoint_returns_flags(): void
    {
        AppSetting::query()->create([
            'key' => 'app_features.force_app_upgrade_enabled',
            'value' => '1',
        ]);

        app(AppSettingsService::class)->loadAppSettingsIntoConfig();

        $this->withHeaders([
            'X-Client-Platform' => 'ios',
            'X-App-Version-Code' => '1',
        ])->getJson('/api/app-config')
            ->assertOk()
            ->assertJsonPath('data.force_app_upgrade_enabled', true)
            ->assertJsonPath('data.platform', 'ios')
            ->assertJsonPath('data.features.wallet_recharge_enabled', true)
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'maintenance_mode_enabled',
                    'force_app_upgrade_enabled',
                    'android_min_version_code',
                    'android_min_version_name',
                    'android_update_message',
                    'ios_min_version_code',
                    'ios_min_version_name',
                    'ios_update_message',
                    'platform',
                    'minimum_version_code',
                    'minimum_version_name',
                    'update_message',
                    'features' => [
                        'video_rooms_enabled',
                        'pk_battles_enabled',
                        'gifts_enabled',
                        'subscriptions_enabled',
                        'entry_effects_enabled',
                        'wallet_recharge_enabled',
                        'host_calling_enabled',
                    ],
                    'platforms' => [
                        'android' => ['minimum_version_code', 'features'],
                        'ios' => ['minimum_version_code', 'features'],
                    ],
                ],
            ]);
    }

    public function test_legacy_app_config_request_defaults_to_android_shape(): void
    {
        config([
            'app_features.android_min_version_code' => 61,
            'app_features.platform.android.wallet_recharge_enabled' => true,
            'app_features.platform.ios.wallet_recharge_enabled' => false,
        ]);

        $this->getJson('/api/app-config')
            ->assertOk()
            ->assertJsonPath('data.platform', 'android')
            ->assertJsonPath('data.minimum_version_code', 61)
            ->assertJsonPath('data.android_min_version_code', 61)
            ->assertJsonPath('data.features.wallet_recharge_enabled', true);
    }

    private function validAppSettingsPayload(array $overrides = []): array
    {
        $payload = [];
        foreach (AppSettingsService::APP_DEFINITIONS as $key => $definition) {
            data_set($payload, $key, $definition['default']);
        }
        foreach ($overrides as $key => $value) {
            data_set($payload, $key, $value);
        }

        return $payload;
    }
}
