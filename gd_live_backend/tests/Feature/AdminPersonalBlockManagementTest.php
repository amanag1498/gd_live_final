<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPersonalBlockManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'agency', 'host', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_admin_can_view_and_filter_personal_blocks(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $blocker = User::factory()->create(['name' => 'Safety Tester', 'email' => 'safety@example.test']);
        $blocked = User::factory()->create(['name' => 'Hidden Host', 'email' => 'hidden@example.test']);
        UserBlock::query()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.moderation.personal-blocks', ['q' => 'Safety Tester']))
            ->assertOk()
            ->assertSee('Personal User Blocks')
            ->assertSee('Safety Tester')
            ->assertSee('Hidden Host')
            ->assertViewHas('rows', fn ($rows) => $rows->total() === 1);
    }

    public function test_non_admin_cannot_view_personal_blocks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.moderation.personal-blocks'))
            ->assertForbidden();
    }

    public function test_admin_can_remove_personal_block_with_audit_record(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $blocker = User::factory()->create();
        $blocked = User::factory()->create();
        $block = UserBlock::query()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.moderation.personal-blocks.destroy', $block), [
                'reason' => 'Resolved support request',
            ])
            ->assertRedirect()
            ->assertSessionHas('ok');

        $this->assertDatabaseMissing('user_blocks', ['id' => $block->id]);
        $this->assertDatabaseHas('admin_action_audits', [
            'admin_user_id' => $admin->id,
            'target_user_id' => $blocked->id,
            'area' => 'moderation',
            'action' => 'personal_block_removed',
            'reason' => 'Resolved support request',
        ]);
    }

    public function test_admin_api_can_list_and_remove_personal_blocks(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $blocker = User::factory()->create(['name' => 'API Blocker']);
        $blocked = User::factory()->create(['name' => 'API Target']);
        $block = UserBlock::query()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked->id,
        ]);
        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/personal-blocks?q=API Blocker')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $block->id)
            ->assertJsonPath('data.0.blocker.name', 'API Blocker')
            ->assertJsonPath('data.0.blocked_user.name', 'API Target');

        $this->deleteJson('/api/admin/personal-blocks/'.$block->id, [
            'reason' => 'API support override',
        ])->assertOk()->assertJsonPath('ok', true);

        $this->assertDatabaseMissing('user_blocks', ['id' => $block->id]);
        $this->assertDatabaseHas('admin_action_audits', [
            'admin_user_id' => $admin->id,
            'action' => 'personal_block_removed',
            'reason' => 'API support override',
        ]);
    }
}
