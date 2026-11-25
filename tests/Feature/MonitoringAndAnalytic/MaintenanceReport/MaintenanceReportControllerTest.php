<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('redirects unauthenticated users to login', function (): void {
    $response = $this->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertRedirect(route('login'));
});

it('forbids unauthorized roles from accessing maintenance report', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Technician->value, 'name' => UserRole::Technician->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($user)->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertForbidden();
});

it('renders maintenance report index for authorized roles', function (UserRole $roleSlug): void {
    $role = Role::factory()->create(['slug' => $roleSlug->value, 'name' => $roleSlug->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertOk()
        ->assertViewIs('pages.dashboard.monitoring-and-analytic.maintenance-report.index')
        ->assertViewHasAll([
            'filters',
            'history',
            'summary',
            'damageFrequency',
            'costAnalysis',
            'preventiveVsReactive',
            'filterOptions',
        ]);
})->with([
    'admin' => [UserRole::Admin],
    'management' => [UserRole::Management],
    'supervisor' => [UserRole::Supervisor],
]);

it('exports maintenance report as csv download', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($user)->get(route('monitoring-and-analytic.maintenance-report.export', [
        'export' => 'csv',
    ]));

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

it('renders printable maintenance report view when export is print', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Management->value, 'name' => UserRole::Management->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.export', [
            'export' => 'print',
        ]));

    $response->assertOk()
        ->assertViewIs('pages.dashboard.monitoring-and-analytic.maintenance-report.print')
        ->assertViewHasAll(['records', 'summary', 'damageFrequency', 'costAnalysis', 'preventiveVsReactive', 'generatedAt']);
});
