<?php

use App\Enums\AssignmentStatus;
use App\Enums\FacilityCondition;
use App\Enums\ReportStatus;
use App\Models\Assignment;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Report;
use App\Models\User;
use App\Services\MonitoringAndAnalytic\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('aggregates kpi summary cards accurately with and without filters', function (): void {
    $locationA = Location::factory()->create();
    $locationB = Location::factory()->create();

    $buildingA = Building::factory()->create(['location_id' => $locationA->id]);
    $buildingB = Building::factory()->create(['location_id' => $locationB->id]);

    $facilityProblemA = Facility::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'condition' => FacilityCondition::MajorDamage,
    ]);
    $facilityGoodA = Facility::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'condition' => FacilityCondition::Good,
    ]);
    $facilityProblemB = Facility::factory()->create([
        'location_id' => $locationB->id,
        'building_id' => $buildingB->id,
        'condition' => FacilityCondition::OutOfOrder,
    ]);

    $activeReport = Report::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'facility_id' => $facilityProblemA->id,
        'status' => ReportStatus::InProgress,
    ]);
    Report::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'facility_id' => $facilityGoodA->id,
        'status' => ReportStatus::Completed,
        'completed_at' => now(),
    ]);
    Report::factory()->create([
        'location_id' => $locationB->id,
        'building_id' => $buildingB->id,
        'facility_id' => $facilityProblemB->id,
        'status' => ReportStatus::Pending,
    ]);
    Report::factory()->create([
        'location_id' => $locationB->id,
        'building_id' => $buildingB->id,
        'facility_id' => $facilityProblemB->id,
        'status' => ReportStatus::Rejected,
    ]);

    $officer = MaintenanceOfficer::factory()->create();
    Assignment::factory()->create([
        'report_id' => $activeReport->id,
        'officer_id' => $officer->id,
        'status' => AssignmentStatus::InProgress,
    ]);
    Assignment::factory()->create([
        'report_id' => $activeReport->id,
        'officer_id' => $officer->id,
        'status' => AssignmentStatus::Completed,
    ]);

    $service = new DashboardService;

    $unfiltered = $service->getKpiSummary();
    expect($unfiltered)->toBe([
        'active_reports' => 2,
        'completed_reports' => 1,
        'problematic_assets' => 2,
        'maintenance_tasks' => 1,
    ]);

    $filteredByLocationA = $service->getKpiSummary(['location_id' => $locationA->id]);
    expect($filteredByLocationA)->toBe([
        'active_reports' => 1,
        'completed_reports' => 1,
        'problematic_assets' => 1,
        'maintenance_tasks' => 1,
    ]);
});

it('calculates periodic report volume trends for chart.js', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-11-15 10:00:00'));

    $location = Location::factory()->create();
    $building = Building::factory()->create(['location_id' => $location->id]);
    $facility = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
    ]);

    Report::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
        'status' => ReportStatus::Completed,
        'created_at' => Carbon::parse('2026-10-05 09:00:00'),
        'completed_at' => Carbon::parse('2026-10-10 12:00:00'),
    ]);

    Report::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
        'status' => ReportStatus::Pending,
        'created_at' => Carbon::parse('2026-11-02 09:00:00'),
        'completed_at' => null,
    ]);

    $service = new DashboardService;
    $trends = $service->getReportTrends([], 3);

    expect($trends['labels'])->toHaveCount(3)
        ->and($trends['datasets']['incoming'])->toBe([0, 1, 1])
        ->and($trends['datasets']['completed'])->toBe([0, 1, 0]);

    Carbon::setTestNow();
});

it('calculates technician task completion performance metrics', function (): void {
    $user1 = User::factory()->create(['name' => 'Budi Teknisi']);
    $officer1 = MaintenanceOfficer::factory()->create([
        'user_id' => $user1->id,
        'employee_code' => 'TEK-001',
    ]);

    $user2 = User::factory()->create(['name' => 'Ani Teknisi']);
    $officer2 = MaintenanceOfficer::factory()->create([
        'user_id' => $user2->id,
        'employee_code' => 'TEK-002',
    ]);

    $report = Report::factory()->create();

    Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer1->id,
        'status' => AssignmentStatus::Completed,
        'started_at' => Carbon::parse('2026-11-20 08:00:00'),
        'completed_at' => Carbon::parse('2026-11-20 12:00:00'),
    ]);
    Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer1->id,
        'status' => AssignmentStatus::InProgress,
    ]);

    Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer2->id,
        'status' => AssignmentStatus::Completed,
        'started_at' => Carbon::parse('2026-11-21 08:00:00'),
        'completed_at' => Carbon::parse('2026-11-21 10:00:00'),
    ]);

    $service = new DashboardService;
    $performance = $service->getTechnicianPerformance();

    expect($performance['summary'])->toBe([
        'total_tasks' => 3,
        'completed_tasks' => 2,
        'completion_rate' => 66.7,
        'avg_resolution_hours' => 3.0,
    ]);

    expect($performance['technicians'])->toHaveCount(2)
        ->and($performance['technicians'][0]['officer_name'])->toBe('Budi Teknisi')
        ->and($performance['technicians'][0]['completion_rate'])->toBe(50.0)
        ->and($performance['technicians'][0]['avg_resolution_hours'])->toBe(4.0);
});

it('returns combined dashboard data and active filter options', function (): void {
    $location = Location::factory()->create(['is_active' => true]);
    $building = Building::factory()->create(['location_id' => $location->id, 'is_active' => true]);
    Facility::factory()->create(['location_id' => $location->id, 'building_id' => $building->id]);
    MaintenanceOfficer::factory()->create(['is_active' => true]);

    $service = new DashboardService;

    $data = $service->getDashboardData();
    expect($data)->toHaveKeys(['kpi', 'trends', 'technician_performance']);

    $options = $service->getFilterOptions();
    expect($options)->toHaveKeys(['locations', 'buildings', 'facilities', 'officers', 'statuses'])
        ->and($options['locations'])->toHaveCount(1)
        ->and($options['buildings'])->toHaveCount(1)
        ->and($options['facilities'])->toHaveCount(1)
        ->and($options['officers'])->toHaveCount(1)
        ->and($options['statuses'])->toBe(ReportStatus::cases());
});
