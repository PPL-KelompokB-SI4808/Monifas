<?php

use App\Enums\RepairExecutionStatus;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Building;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Repair;
use App\Models\RepairMaterial;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the maintenance report page with analytics components and real repair data', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $location = Location::factory()->create(['name' => 'Kampus Timur']);
    $building = Building::factory()->create(['location_id' => $location->id, 'name' => 'Gedung Lab']);
    $category = FacilityCategory::factory()->create(['name' => 'Pendingin Ruangan']);
    $facility = Facility::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_category_id' => $category->id,
        'name' => 'Chiller Lab 01',
    ]);

    $techUser = User::factory()->create(['name' => 'Dedi Kurniawan']);
    $officer = MaintenanceOfficer::factory()->create(['user_id' => $techUser->id]);
    $report = Report::factory()->create([
        'location_id' => $location->id,
        'building_id' => $building->id,
        'facility_id' => $facility->id,
    ]);
    $assignment = Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer->id,
    ]);
    $repair = Repair::create([
        'assignment_id' => $assignment->id,
        'report_id' => $report->id,
        'facility_id' => $facility->id,
        'officer_id' => $officer->id,
        'started_at' => now()->subHours(2),
        'ended_at' => now(),
        'technical_action' => 'Ganti kompresor unit',
        'repair_result' => 'Berfungsi normal',
        'resulting_facility_condition' => 'good',
        'status' => RepairExecutionStatus::Completed,
    ]);
    RepairMaterial::create([
        'repair_id' => $repair->id,
        'material_name' => 'Kompresor Inverter',
        'material_type' => 'Sparepart HVAC',
        'unit' => 'unit',
        'quantity' => 1,
        'unit_price' => 1250000,
        'subtotal' => 1250000,
    ]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertStatus(200)
        ->assertSee('Laporan &amp; Analisis Pemeliharaan', false)
        ->assertSee('Ekspor / Cetak Laporan')
        ->assertSee('Total Perbaikan')
        ->assertSee('Perbaikan Selesai')
        ->assertSee('Total Biaya Material')
        ->assertSee('Preventive vs Reaktif / Darurat')
        ->assertSee('Frekuensi Kerusakan Fasilitas')
        ->assertSee('Analisis Biaya Perbaikan &amp; Konsumsi Material', false)
        ->assertSee('Riwayat Pemeliharaan &amp; Perbaikan', false)
        ->assertSee('Chiller Lab 01')
        ->assertSee('Kompresor Inverter')
        ->assertSee('Dedi Kurniawan')
        ->assertSee('1.250.000');
});

it('filters maintenance report by location and exports filtered csv and print views', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Management->value, 'name' => UserRole::Management->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $locationA = Location::factory()->create(['name' => 'Area A']);
    $locationB = Location::factory()->create(['name' => 'Area B']);
    $buildingA = Building::factory()->create(['location_id' => $locationA->id]);
    $facilityA = Facility::factory()->create([
        'location_id' => $locationA->id,
        'building_id' => $buildingA->id,
        'name' => 'Pompa Air A',
    ]);

    $officer = MaintenanceOfficer::factory()->create();
    $assignment = Assignment::factory()->create([
        'report_id' => null,
        'officer_id' => $officer->id,
    ]);
    Repair::create([
        'assignment_id' => $assignment->id,
        'report_id' => null,
        'facility_id' => $facilityA->id,
        'officer_id' => $officer->id,
        'started_at' => now()->subHour(),
        'ended_at' => now(),
        'technical_action' => 'Inspeksi rutin pompa',
        'repair_result' => 'Aman',
        'resulting_facility_condition' => 'good',
        'status' => RepairExecutionStatus::Completed,
    ]);

    $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.index', ['location_id' => $locationB->id]))
        ->assertOk()
        ->assertSee('Belum ada riwayat pemeliharaan')
        ->assertDontSee('Pompa Air A');

    $csvResponse = $this->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.export', [
            'location_id' => $locationA->id,
            'export' => 'csv',
        ]));
    $csvResponse->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.export', [
            'location_id' => $locationA->id,
            'export' => 'print',
        ]))
        ->assertOk()
        ->assertSee('Pompa Air A');
});
