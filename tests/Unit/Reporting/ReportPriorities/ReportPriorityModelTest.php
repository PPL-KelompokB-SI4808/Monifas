<?php

use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('model report priority memiliki fillable attributes yang tepat', function () {
    $priority = new ReportPriority([
        'name' => 'Darurat',
        'level' => PriorityLevel::Emergency,
        'sla_response_hours' => 1,
        'sla_resolution_hours' => 4,
        'badge_variant' => 'danger',
        'sort_order' => 1,
        'description' => 'Kerusakan darurat',
        'is_active' => true,
    ]);

    expect($priority->name)->toBe('Darurat')
        ->and($priority->level)->toBe(PriorityLevel::Emergency)
        ->and($priority->sla_response_hours)->toBe(1)
        ->and($priority->sla_resolution_hours)->toBe(4)
        ->and($priority->badge_variant)->toBe('danger')
        ->and($priority->sort_order)->toBe(1)
        ->and($priority->description)->toBe('Kerusakan darurat')
        ->and($priority->is_active)->toBeTrue();
});

test('model report priority mendukung soft deletes', function () {
    $priority = ReportPriority::factory()->create();

    $priority->delete();

    expect($priority->trashed())->toBeTrue()
        ->and(ReportPriority::count())->toBe(0)
        ->and(ReportPriority::withTrashed()->count())->toBe(1);

    $priority->restore();

    expect($priority->trashed())->toBeFalse()
        ->and(ReportPriority::count())->toBe(1);
});

test('scope ordered by urgency mengurutkan prioritas dari darurat ke rendah', function () {
    // Buat data dengan urutan acak
    ReportPriority::factory()->create([
        'level' => PriorityLevel::Low,
        'sort_order' => 4,
    ]);

    ReportPriority::factory()->create([
        'level' => PriorityLevel::Emergency,
        'sort_order' => 1,
    ]);

    ReportPriority::factory()->create([
        'level' => PriorityLevel::High,
        'sort_order' => 2,
    ]);

    ReportPriority::factory()->create([
        'level' => PriorityLevel::Medium,
        'sort_order' => 3,
    ]);

    $ordered = ReportPriority::orderedByUrgency()->get();

    expect($ordered)->toHaveCount(4)
        ->and($ordered[0]->level)->toBe(PriorityLevel::Emergency)
        ->and($ordered[0]->sort_order)->toBe(1)
        ->and($ordered[1]->level)->toBe(PriorityLevel::High)
        ->and($ordered[1]->sort_order)->toBe(2)
        ->and($ordered[2]->level)->toBe(PriorityLevel::Medium)
        ->and($ordered[2]->sort_order)->toBe(3)
        ->and($ordered[3]->level)->toBe(PriorityLevel::Low)
        ->and($ordered[3]->sort_order)->toBe(4);
});

test('enum priority level memiliki label dan nilai yang sesuai', function () {
    expect(PriorityLevel::Emergency->value)->toBe('emergency')
        ->and(PriorityLevel::Emergency->label())->toBe('Darurat')
        ->and(PriorityLevel::High->value)->toBe('high')
        ->and(PriorityLevel::High->label())->toBe('Tinggi')
        ->and(PriorityLevel::Medium->value)->toBe('medium')
        ->and(PriorityLevel::Medium->label())->toBe('Sedang')
        ->and(PriorityLevel::Low->value)->toBe('low')
        ->and(PriorityLevel::Low->label())->toBe('Rendah')
        ->and(PriorityLevel::values())->toBe(['low', 'medium', 'high', 'emergency']);
});
