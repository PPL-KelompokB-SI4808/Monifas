<?php

use App\Enums\AssignmentStatus;
use App\Enums\FacilityCondition;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders dashboard kpi cards, chart payload, filter options, and technician performance table', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Management->value, 'name' => UserRole::Management->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $location = Location::factory()->create(['name' => 'Kampus Utama']);
    $building = Building::factory()->create(['location_id' => $location->id, 'name' => 'Gedung Teknik']);
    $facility = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'name' => 'AC Central Lantai 2',
        'condition' => FacilityCondition::MajorDamage,
    ]);

    $techUser = User::factory()->create(['name' => 'Rudi Hartono']);
    $officer = MaintenanceOfficer::factory()->create([
        'user_id' => $techUser->id,
        'employee_code' => 'TEK-101',
    ]);

    $report = Report::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
        'status' => ReportStatus::InProgress,
    ]);

    Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer->id,
        'status' => AssignmentStatus::InProgress,
    ]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.dashboard.index'));

    $response->assertOk()
        ->assertViewIs('pages.dashboard.monitoring-and-analytic.dashboard.index')
        ->assertSee('Kampus Utama')
        ->assertSee('Gedung Teknik')
        ->assertSee('AC Central Lantai 2')
        ->assertSee('Rudi Hartono')
        ->assertSee('TEK-101')
        ->assertSee('monitoringAndAnalyticDashboard(', false);
});

it('filters dashboard view data by location and shows empty technician state when no tasks match', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $locationA = Location::factory()->create(['name' => 'Zona Utara']);
    $locationB = Location::factory()->create(['name' => 'Zona Selatan']);
    $buildingA = Building::factory()->create(['location_id' => $locationA->id]);
    $facilityA = Facility::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
    ]);

    $techUser = User::factory()->create(['name' => 'Siti Teknisi']);
    $officer = MaintenanceOfficer::factory()->create([
        'user_id' => $techUser->id,
        'employee_code' => 'TEK-202',
    ]);

    $reportA = Report::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'facility_id' => $facilityA->id,
        'status' => ReportStatus::Completed,
        'completed_at' => now(),
    ]);

    Assignment::factory()->create([
        'report_id' => $reportA->id,
        'officer_id' => $officer->id,
        'status' => AssignmentStatus::Completed,
        'completed_at' => now(),
    ]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.dashboard.index', [
            'location_id' => $locationB->id,
        ]));

    $response->assertOk()
        ->assertSee('Belum ada data penugasan teknisi')
        ->assertDontSee('TEK-202');
});
