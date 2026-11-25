<?php

use App\Enums\RepairExecutionStatus;
use App\Models\Assignment;
use App\Models\Building;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Repair;
use App\Models\RepairMaterial;
use App\Models\Report;
use App\Services\MonitoringAndAnalytic\MaintenanceReport\MaintenanceReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function createRepairWithMaterial(array $overrides = []): Repair
{
    $location = $overrides['location'] ?? Location::factory()->create();
    $building = $overrides['building'] ?? Building::factory()->create(['location_id' => $location->id]);
    $category = $overrides['category'] ?? FacilityCategory::factory()->create();
    $facility = $overrides['facility'] ?? Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_category_id' => $category->id,
    ]);
    $officer = $overrides['officer'] ?? MaintenanceOfficer::factory()->create();
    $report = array_key_exists('report', $overrides)
        ? $overrides['report']
        : Report::factory()->create([
            'location_id' => $location->id,
            'building_id' => $building->id,
            'facility_id' => $facility->id,
        ]);

    $assignment = Assignment::factory()->create([
        'report_id' => $report?->id,
        'maintenance_schedule_id' => $overrides['maintenance_schedule_id'] ?? null,
        'officer_id' => $officer->id,
    ]);

    $repair = Repair::create([
        'assignment_id' => $assignment->id,
        'report_id' => $report?->id,
        'facility_id' => $facility->id,
        'officer_id' => $officer->id,
        'started_at' => $overrides['started_at'] ?? now()->subHours(3),
        'ended_at' => $overrides['ended_at'] ?? now(),
        'technical_action' => $overrides['technical_action'] ?? 'Penggantian komponen rusak',
        'repair_result' => 'Normal kembali',
        'resulting_facility_condition' => 'good',
        'status' => $overrides['status'] ?? RepairExecutionStatus::Completed,
    ]);

    if (isset($overrides['material'])) {
        RepairMaterial::create(array_merge([
            'repair_id' => $repair->id,
            'material_name' => 'Filter Udara',
            'material_type' => 'Sparepart HVAC',
            'unit' => 'pcs',
            'quantity' => 2,
            'unit_price' => 250000,
            'subtotal' => 500000,
        ], $overrides['material']));
    }

    return $repair;
}

it('analyzes damage frequency per facility and category', function (): void {
    $location = Location::factory()->create();
    $building = Building::factory()->create(['location_id' => $location->id]);
    $catA = FacilityCategory::factory()->create(['name' => 'HVAC']);
    $catB = FacilityCategory::factory()->create(['name' => 'Listrik']);

    $facA = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_category_id' => $catA->id,
        'name' => 'Chiller #1',
    ]);
    $facB = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_category_id' => $catB->id,
        'name' => 'Panel MDP',
    ]);

    createRepairWithMaterial(['facility' => $facA, 'location' => $location, 'building' => $building, 'category' => $catA]);
    createRepairWithMaterial(['facility' => $facA, 'location' => $location, 'building' => $building, 'category' => $catA]);
    createRepairWithMaterial(['facility' => $facB, 'location' => $location, 'building' => $building, 'category' => $catB]);

    $service = new MaintenanceReportService;
    $freq = $service->getDamageFrequency();

    expect($freq['by_facility'])->toHaveCount(2)
        ->and($freq['by_facility'][0]['facility_name'])->toBe('Chiller #1')
        ->and($freq['by_facility'][0]['repair_count'])->toBe(2)
        ->and($freq['by_category'])->toHaveCount(2)
        ->and($freq['by_category'][0]['category_name'])->toBe('HVAC');
});

it('analyzes repair costs and material consumption', function (): void {
    createRepairWithMaterial([
        'material' => [
            'material_name' => 'Kontaktor 30A',
            'material_type' => 'Kelistrikan',
            'unit' => 'pcs',
            'quantity' => 2,
            'unit_price' => 300000,
            'subtotal' => 600000,
        ],
    ]);
    createRepairWithMaterial([
        'material' => [
            'material_name' => 'Freon R32',
            'material_type' => 'HVAC',
            'unit' => 'tabung',
            'quantity' => 1,
            'unit_price' => 450000,
            'subtotal' => 450000,
        ],
    ]);

    $service = new MaintenanceReportService;
    $analysis = $service->getCostAndMaterialAnalysis();

    expect($analysis['total_cost'])->toBe(1050000.0)
        ->and($analysis['total_material_items'])->toBe(2)
        ->and($analysis['total_quantity'])->toBe(3.0)
        ->and($analysis['by_material_type'])->toHaveCount(2)
        ->and($analysis['top_materials'][0]['material_name'])->toBe('Kontaktor 30A');
});

it('compares preventive maintenance versus reactive repairs', function (): void {
    createRepairWithMaterial([
        'report' => null,
        'material' => ['subtotal' => 200000],
    ]);
    createRepairWithMaterial([
        'material' => ['subtotal' => 800000],
    ]);

    $service = new MaintenanceReportService;
    $comparison = $service->getPreventiveVsReactive();

    expect($comparison['total'])->toBe(2)
        ->and($comparison['preventive']['count'])->toBe(1)
        ->and($comparison['preventive']['percentage'])->toBe(50.0)
        ->and($comparison['preventive']['total_cost'])->toBe(200000.0)
        ->and($comparison['reactive']['count'])->toBe(1)
        ->and($comparison['reactive']['total_cost'])->toBe(800000.0);
});

it('streams a utf-8 bom csv export of maintenance history', function (): void {
    createRepairWithMaterial([
        'technical_action' => 'Pembersihan evaporator',
        'material' => ['subtotal' => 150000],
    ]);

    $service = new MaintenanceReportService;
    $response = $service->exportCsv();

    expect($response)->toBeInstanceOf(StreamedResponse::class);

    ob_start();
    $response->sendContent();
    $content = (string) ob_get_clean();

    expect($content)->toContain('ID Perbaikan')
        ->and($content)->toContain('Pembersihan evaporator')
        ->and($content)->toContain('150000.00');
});
