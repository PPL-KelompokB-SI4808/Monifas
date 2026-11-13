<?php

use App\Enums\ReportStatus;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('model report memiliki fillable attributes dan relasi yang tepat', function () {
    $reporter = User::factory()->create();
    $supervisor = User::factory()->create();
    $location = Location::factory()->create();
    $building = Building::factory()->create(['location_id' => $location->id]);
    $facility = Facility::factory()->create(['location_id' => $location->id, 'building_id' => $building->id]);
    $category = DamageCategory::factory()->create();
    $priority = ReportPriority::factory()->emergency()->create();

    $report = Report::create([
        'ticket_number' => 'RPT-202611-0001',
        'reporter_id' => $reporter->id,
        'location_id' => $location->id,
        'building_id' => $building->id,
        'room_id' => null,
        'facility_id' => $facility->id,
        'damage_category_id' => $category->id,
        'priority_id' => $priority->id,
        'title' => 'Kerusakan Pipa Air Bocor',
        'description' => 'Pipa air di koridor utama mengalami kebocoran deras.',
        'status' => ReportStatus::Pending,
        'verified_by' => $supervisor->id,
        'verified_at' => now(),
        'rejection_reason' => null,
        'completed_at' => null,
    ]);

    expect($report->ticket_number)->toBe('RPT-202611-0001')
        ->and($report->status)->toBe(ReportStatus::Pending)
        ->and($report->reporter->id)->toBe($reporter->id)
        ->and($report->verifiedBy->id)->toBe($supervisor->id)
        ->and($report->location->id)->toBe($location->id)
        ->and($report->building->id)->toBe($building->id)
        ->and($report->facility->id)->toBe($facility->id)
        ->and($report->damageCategory->id)->toBe($category->id)
        ->and($report->priority->id)->toBe($priority->id);
});

test('model report mendukung scope pending verified dan open', function () {
    Report::factory()->create(['status' => ReportStatus::Pending]);
    Report::factory()->verified()->create();
    Report::factory()->completed()->create();
    Report::factory()->rejected()->create();

    expect(Report::pending()->count())->toBe(1)
        ->and(Report::verified()->count())->toBe(1)
        ->and(Report::open()->count())->toBe(2);
});

test('factory report dapat membuat status bervariasi dengan state helper', function () {
    $verified = Report::factory()->verified()->create();
    expect($verified->status)->toBe(ReportStatus::Verified)
        ->and($verified->verified_at)->not->toBeNull()
        ->and($verified->verified_by)->not->toBeNull();

    $rejected = Report::factory()->rejected('Foto bukti tidak jelas')->create();
    expect($rejected->status)->toBe(ReportStatus::Rejected)
        ->and($rejected->rejection_reason)->toBe('Foto bukti tidak jelas');
});
