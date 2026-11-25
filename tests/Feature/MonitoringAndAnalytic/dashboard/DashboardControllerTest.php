<?php

use App\Enums\UserRole;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('redirects unauthenticated users to login', function (): void {
    $response = $this->get(route('monitoring-and-analytic.dashboard.index'));

    $response->assertRedirect(route('login'));
});

it('forbids unauthorized roles from accessing the dashboard', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::FacilityUser->value, 'name' => UserRole::FacilityUser->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($user)->get(route('monitoring-and-analytic.dashboard.index'));

    $response->assertForbidden();
});

it('renders the dashboard for authorized roles with analytics data', function (UserRole $roleSlug): void {
    $role = Role::factory()->create(['slug' => $roleSlug->value, 'name' => $roleSlug->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);
    $location = Location::factory()->create();

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.dashboard.index', [
            'location_id' => $location->id,
        ]));

    $response->assertOk()
        ->assertViewIs('pages.dashboard.monitoring-and-analytic.dashboard.index')
        ->assertViewHasAll(['filters', 'kpi', 'trends', 'technicianPerformance', 'filterOptions']);
})->with([
    'admin' => [UserRole::Admin],
    'management' => [UserRole::Management],
    'supervisor' => [UserRole::Supervisor],
]);

it('validates filter query parameters on the dashboard route', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($user)->get(route('monitoring-and-analytic.dashboard.index', [
        'start_date' => '2026-11-25',
        'end_date' => '2026-11-01',
    ]));

    $response->assertSessionHasErrors('end_date');
});
