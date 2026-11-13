<?php

use App\DTOs\Reporting\ReportPriorityData;
use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use App\Services\Reporting\ReportPriorities\ReportPriorityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new ReportPriorityService;
});

test('service memvalidasi batasan jam sla dengan benar', function () {
    // Valid: resolution >= response
    expect(fn () => $this->service->validateSlaHours(4, 24))->not->toThrow(InvalidArgumentException::class);
    expect(fn () => $this->service->validateSlaHours(4, 4))->not->toThrow(InvalidArgumentException::class);

    // Invalid: resolution < response
    expect(fn () => $this->service->validateSlaHours(10, 5))
        ->toThrow(InvalidArgumentException::class, 'Target jam penyelesaian (SLA Resolution) tidak boleh lebih cepat daripada target jam respon.');

    // Invalid: response < 1
    expect(fn () => $this->service->validateSlaHours(0, 5))
        ->toThrow(InvalidArgumentException::class, 'Target jam respon awal (SLA Response) minimal 1 jam.');
});

test('service menentukan urutan prioritas otomatis dari darurat ke rendah', function () {
    expect($this->service->resolveSortOrder(PriorityLevel::Emergency))->toBe(1)
        ->and($this->service->resolveSortOrder(PriorityLevel::High))->toBe(2)
        ->and($this->service->resolveSortOrder(PriorityLevel::Medium))->toBe(3)
        ->and($this->service->resolveSortOrder(PriorityLevel::Low))->toBe(4);

    // Jika explicit sort order diberikan, gunakan explicit order
    expect($this->service->resolveSortOrder(PriorityLevel::Low, 10))->toBe(10);
});

test('service dapat menyimpan prioritas baru dengan kalkulasi urutan otomatis', function () {
    $dto = new ReportPriorityData(
        name: 'Prioritas Darurat Kampus',
        level: PriorityLevel::Emergency,
        slaResponseHours: 1,
        slaResolutionHours: 3,
        badgeVariant: 'danger',
        sortOrder: null, // dibiarkan null untuk pengurutan otomatis
        description: 'Prioritas darurat otomatis',
        isActive: true,
    );

    $created = $this->service->store($dto);

    expect($created->exists)->toBeTrue()
        ->and($created->sort_order)->toBe(1)
        ->and($created->level)->toBe(PriorityLevel::Emergency)
        ->and($created->sla_response_hours)->toBe(1)
        ->and($created->sla_resolution_hours)->toBe(3);
});

test('service melempar exception saat menyimpan dengan sla resolution lebih kecil dari response', function () {
    $dto = new ReportPriorityData(
        name: 'Invalid SLA',
        level: PriorityLevel::High,
        slaResponseHours: 10,
        slaResolutionHours: 2,
        badgeVariant: 'warning',
    );

    expect(fn () => $this->service->store($dto))
        ->toThrow(InvalidArgumentException::class);
});

test('service dapat memperbarui data prioritas dan memvalidasi ulang sla', function () {
    $priority = ReportPriority::factory()->create([
        'level' => PriorityLevel::Medium,
        'sla_response_hours' => 12,
        'sla_resolution_hours' => 48,
    ]);

    $dto = new ReportPriorityData(
        name: 'Prioritas Sedang Diperbarui',
        level: PriorityLevel::Medium,
        slaResponseHours: 6,
        slaResolutionHours: 24,
        badgeVariant: 'info',
        sortOrder: 3,
        description: 'Deskripsi baru',
        isActive: false,
    );

    $updated = $this->service->update($priority, $dto);

    expect($updated->name)->toBe('Prioritas Sedang Diperbarui')
        ->and($updated->sla_response_hours)->toBe(6)
        ->and($updated->sla_resolution_hours)->toBe(24)
        ->and($updated->is_active)->toBeFalse();
});

test('service dapat melakukan toggle status aktif', function () {
    $priority = ReportPriority::factory()->create(['is_active' => true]);

    $toggled = $this->service->toggleStatus($priority);
    expect($toggled->is_active)->toBeFalse();

    $toggledAgain = $this->service->toggleStatus($toggled);
    expect($toggledAgain->is_active)->toBeTrue();
});

test('service dapat menyinkronkan urutan standar darurat ke rendah', function () {
    // Buat data dengan sort_order acak
    $p1 = ReportPriority::factory()->create(['level' => PriorityLevel::Low, 'sort_order' => 1]);
    $p2 = ReportPriority::factory()->create(['level' => PriorityLevel::Emergency, 'sort_order' => 4]);

    $this->service->syncStandardUrgencyOrder();

    expect($p1->fresh()->sort_order)->toBe(4)
        ->and($p2->fresh()->sort_order)->toBe(1);
});

test('service dapat mengambil daftar prioritas terpaginasi dengan filter', function () {
    ReportPriority::factory()->create([
        'name' => 'Kerusakan Darurat Pipa',
        'level' => PriorityLevel::Emergency,
        'is_active' => true,
    ]);

    ReportPriority::factory()->create([
        'name' => 'Kerusakan Ringan Meja',
        'level' => PriorityLevel::Low,
        'is_active' => false,
    ]);

    $results = $this->service->getPaginatedList(['search' => 'Pipa']);
    expect($results->total())->toBe(1)
        ->and($results->first()->name)->toBe('Kerusakan Darurat Pipa');

    $activeOnly = $this->service->getPaginatedList(['is_active' => 'true']);
    expect($activeOnly->total())->toBe(1);
});
