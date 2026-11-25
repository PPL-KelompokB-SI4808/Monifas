<?php

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Http\Requests\MonitoringAndAnalytic\Dashboard\DashboardFilterRequest;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('authorizes admin, management, and supervisor roles', function (UserRole $roleSlug, bool $expected): void {
    $role = Role::factory()->create(['slug' => $roleSlug->value, 'name' => $roleSlug->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET');
    $request->setUserResolver(fn () => $user->load('role'));

    expect($request->authorize())->toBe($expected);
})->with([
    'admin' => [UserRole::Admin, true],
    'management' => [UserRole::Management, true],
    'supervisor' => [UserRole::Supervisor, true],
    'technician' => [UserRole::Technician, false],
    'facility user' => [UserRole::FacilityUser, false],
]);

it('denies unauthenticated users', function (): void {
    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET');
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('passes validation when all filter fields are empty or null', function (): void {
    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET', [
        'start_date' => null,
        'end_date' => null,
        'location_id' => null,
        'building_id' => null,
        'facility_id' => null,
        'status' => null,
        'officer_id' => null,
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
});

it('passes validation with valid filter values', function (): void {
    $location = Location::factory()->create();
    $building = Building::factory()->create(['location_id' => $location->id]);
    $facility = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
    ]);
    $officer = MaintenanceOfficer::factory()->create();

    $payload = [
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-25',
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
        'status' => ReportStatus::InProgress->value,
        'officer_id' => $officer->id,
    ];

    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET', $payload);
    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
});

it('passes validation when end_date is provided without start_date', function (): void {
    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET', [
        'start_date' => null,
        'end_date' => '2026-11-25',
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when end_date is before start_date', function (): void {
    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET', [
        'start_date' => '2026-11-25',
        'end_date' => '2026-11-01',
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('end_date'))->toBeTrue();
});

it('fails validation for nonexistent foreign keys and invalid status', function (): void {
    $request = DashboardFilterRequest::create('/dashboard/monitoring-and-analytic/dashboard', 'GET', [
        'location_id' => 999999,
        'building_id' => 999999,
        'facility_id' => 999999,
        'status' => 'invalid_status',
        'officer_id' => 999999,
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toContain(
            'location_id',
            'building_id',
            'facility_id',
            'status',
            'officer_id'
        );
});
