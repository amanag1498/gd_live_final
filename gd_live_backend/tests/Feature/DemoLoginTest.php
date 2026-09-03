<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DemoLoginTest extends TestCase
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

    public function test_demo_login_is_rejected_when_disabled(): void
    {
        config([
            'app_features.demo_login_enabled' => false,
            'app_features.demo_login_email' => 'reviewer@gdlive.test',
        ]);

        $this->postJson('/api/auth/demo/login', [
            'email' => 'reviewer@gdlive.test',
        ])->assertForbidden()
            ->assertJsonPath('code', 'demo_login_unavailable');
    }

    public function test_demo_login_rejects_an_email_that_does_not_match_configuration(): void
    {
        User::factory()->create(['email' => 'reviewer@gdlive.test']);
        config([
            'app_features.demo_login_enabled' => true,
            'app_features.demo_login_email' => 'reviewer@gdlive.test',
        ]);

        $this->postJson('/api/auth/demo/login', [
            'email' => 'someone-else@gdlive.test',
        ])->assertForbidden()
            ->assertJsonPath('code', 'demo_login_unavailable');
    }

    public function test_demo_login_rejects_a_blocked_demo_account(): void
    {
        User::factory()->create([
            'email' => 'reviewer@gdlive.test',
            'is_blocked' => true,
        ]);
        config([
            'app_features.demo_login_enabled' => true,
            'app_features.demo_login_email' => 'reviewer@gdlive.test',
        ]);

        $this->postJson('/api/auth/demo/login', [
            'email' => 'reviewer@gdlive.test',
        ])->assertStatus(423)
            ->assertJsonPath('error', 'blocked');
    }

    public function test_demo_login_issues_a_token_for_the_configured_existing_user(): void
    {
        $user = User::factory()->create([
            'name' => 'App Reviewer',
            'email' => 'reviewer@gdlive.test',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
        config([
            'app_features.demo_login_enabled' => true,
            'app_features.demo_login_email' => 'reviewer@gdlive.test',
        ]);

        $response = $this->postJson('/api/auth/demo/login', [
            'email' => 'REVIEWER@gdlive.test',
            'device_name' => 'app-review',
        ])->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('is_new_user', false)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', 'reviewer@gdlive.test')
            ->assertJsonPath('user.is_blocked', false);

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'app-review',
        ]);
    }
}
