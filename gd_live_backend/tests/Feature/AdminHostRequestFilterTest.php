<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\HostRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminHostRequestFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
    }

    public function test_admin_can_search_host_requests_across_applicant_and_request_fields(): void
    {
        $admin = $this->admin();
        $agency = $this->agency('North Talent');
        $matching = $this->hostRequest($agency, [
            'name' => 'Aman Applicant',
            'email' => 'aman-host@example.com',
            'stage_name' => 'Alpha Stage',
            'contact_phone' => '9876501234',
            'city' => 'Jaipur',
        ]);
        $other = $this->hostRequest($agency, [
            'name' => 'Other Applicant',
            'email' => 'other-host@example.com',
            'stage_name' => 'Beta Stage',
        ]);

        foreach (['aman-host@example.com', 'Alpha Stage', '9876501234', 'Jaipur', (string) $matching->id] as $search) {
            $this->actingAs($admin)
                ->get(route('admin.host-requests.index', ['q' => $search]))
                ->assertOk()
                ->assertSee('Alpha Stage')
                ->assertDontSee('Beta Stage');
        }

        $this->assertNotSame($matching->id, $other->id);
    }

    public function test_status_agency_and_submitted_date_filters_can_be_combined(): void
    {
        $admin = $this->admin();
        $north = $this->agency('North Talent');
        $south = $this->agency('South Talent');

        $matching = $this->hostRequest($north, [
            'stage_name' => 'Matching Host',
            'status' => 'approved',
            'created_at' => '2026-07-15 12:00:00',
        ]);
        $this->hostRequest($north, [
            'stage_name' => 'Wrong Status Host',
            'status' => 'pending',
            'created_at' => '2026-07-15 12:00:00',
        ]);
        $this->hostRequest($south, [
            'stage_name' => 'Wrong Agency Host',
            'status' => 'approved',
            'created_at' => '2026-07-15 12:00:00',
        ]);
        $this->hostRequest($north, [
            'stage_name' => 'Wrong Date Host',
            'status' => 'approved',
            'created_at' => '2026-07-10 12:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.host-requests.index', [
            'status' => 'approved',
            'agency_id' => $north->id,
            'date_from' => '2026-07-15',
            'date_to' => '2026-07-15',
            'per_page' => 50,
        ]));

        $response
            ->assertOk()
            ->assertSee('Matching Host')
            ->assertDontSee('Wrong Status Host')
            ->assertDontSee('Wrong Agency Host')
            ->assertDontSee('Wrong Date Host');

        $this->assertSame([$matching->id], $response->viewData('requests')->pluck('id')->all());
        $this->assertSame(50, $response->viewData('filters')['per_page']);
    }

    public function test_invalid_date_range_is_rejected_and_regular_users_are_forbidden(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.host-requests.index', ['date_to' => '2026-07-19']))
            ->assertOk();

        $this->actingAs($admin)
            ->from(route('admin.host-requests.index'))
            ->get(route('admin.host-requests.index', [
                'date_from' => '2026-07-20',
                'date_to' => '2026-07-19',
            ]))
            ->assertRedirect(route('admin.host-requests.index'))
            ->assertSessionHasErrors('date_to');

        $this->actingAs(User::factory()->create())
            ->get(route('admin.host-requests.index'))
            ->assertForbidden();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function agency(string $name): Agency
    {
        return Agency::query()->create([
            'owner_user_id' => User::factory()->create()->id,
            'name' => $name,
        ]);
    }

    private function hostRequest(Agency $agency, array $attributes): HostRequest
    {
        $user = User::factory()->create([
            'name' => $attributes['name'] ?? $attributes['stage_name'] ?? 'Host Applicant',
            'email' => $attributes['email'] ?? fake()->unique()->safeEmail(),
        ]);

        $request = HostRequest::query()->create([
            'user_id' => $user->id,
            'agency_id' => $agency->id,
            'stage_name' => $attributes['stage_name'] ?? 'Host Stage',
            'contact_phone' => $attributes['contact_phone'] ?? null,
            'country' => $attributes['country'] ?? 'India',
            'city' => $attributes['city'] ?? null,
            'about' => 'Host request filter test.',
            'status' => $attributes['status'] ?? 'pending',
        ]);

        if (isset($attributes['created_at'])) {
            $request->forceFill([
                'created_at' => $attributes['created_at'],
                'updated_at' => $attributes['created_at'],
            ])->saveQuietly();
        }

        return $request;
    }
}
