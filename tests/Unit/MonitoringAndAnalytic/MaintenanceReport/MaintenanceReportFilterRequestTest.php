<?php

use App\Enums\RepairExecutionStatus;
use App\Enums\UserRole;
use App\Http\Requests\MonitoringAndAnalytic\MaintenanceReport\MaintenanceReportFilterRequest;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\FacilityCategory;
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

    $request = MaintenanceReportFilterRequest::create('/dashboard/monitoring-and-analytic/maintenance-report', 'GET');
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
    $request = MaintenanceReportFilterRequest::create('/dashboard/monitoring-and-analytic/maintenance-report', 'GET');
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('passes validation when all filter fields are null or omitted', function (): void {
    $request = MaintenanceReportFilterRequest::create('/dashboard/monitoring-and-analytic/maintenance-report', 'GET', [
        'period' => null,
        'start_date' => null,
        'end_date' => null,
        'location_id' => null,
        'building_id' => null,
        'facility_id' => null,
        'facility_category_id' => null,
        'damage_category_id' => null,
        'officer_id' => null,
        'status' => null,
        'search' => null,
        'export' => null,
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
});

it('passes validation with valid filter parameters', function (): void {
    $location = Location::factory()->create();
    $building = Building::factory()->create(['location_id' => $location->id]);
    $facilityCategory = FacilityCategory::factory()->create();
    $damageCategory = DamageCategory::factory()->create();
    $facility = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_category_id' => $facilityCategory->id,
    ]);
    $officer = MaintenanceOfficer::factory()->create();

    $request = MaintenanceReportFilterRequest::create('/dashboard/monitoring-and-analytic/maintenance-report', 'GET', [
        'period' => 'quarter',
        'start_date' => '2026-10-01',
        'end_date' => '2026-11-25',
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
        'facility_category_id' => $facilityCategory->id,
        'damage_category_id' => $damageCategory->id,
        'officer_id' => $officer->id,
        'status' => RepairExecutionStatus::Completed->value,
        'search' => 'Chiller',
        'export' => 'csv',
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
});

it('fails validation for invalid period, date order, foreign keys, status, and export format', function (): void {
    $request = MaintenanceReportFilterRequest::create('/dashboard/monitoring-and-analytic/maintenance-report', 'GET', [
        'period' => 'decade',
        'start_date' => '2026-11-25',
        'end_date' => '2026-11-01',
        'location_id' => 999999,
        'building_id' => 999999,
        'facility_id' => 999999,
        'facility_category_id' => 999999,
        'damage_category_id' => 999999,
        'officer_id' => 999999,
        'status' => 'unknown_status',
        'export' => 'xlsx',
    ]);

    $validator = Validator::make($request->all(), $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toContain(
            'period',
            'end_date',
            'location_id',
            'building_id',
            'facility_id',
            'facility_category_id',
            'damage_category_id',
            'officer_id',
            'status',
            'export'
        );
});
