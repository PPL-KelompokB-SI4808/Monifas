<?php

use App\Enums\OfficerAvailability;
use App\Models\MaintenanceOfficer;
use App\Models\User;
use App\Services\Maintenance\Officers\OfficerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('all officers are returned newest first with their users loaded', function () {
    $users = User::factory()->count(3)->create();
    $officerIds = [];

    foreach ($users as $index => $user) {
        $officer = MaintenanceOfficer::create([
            'user_id' => $user->id,
            'employee_code' => sprintf('MNT-LIST-%02d', $index + 1),
        ]);
        $officerIds[] = $officer->id;
    }

    $officers = (new OfficerService)->getAllOfficers();

    expect($officers)->toBeInstanceOf(Collection::class);
    expect($officers)->toHaveCount(3);
    expect($officers->modelKeys())->toBe(array_reverse($officerIds));

    foreach ($officers as $officer) {
        expect($officer->relationLoaded('user'))->toBeTrue();
        expect($officer->user->id)->toBe($officer->user_id);
    }
});

test('officers are paginated by 15 per page by default', function () {
    $users = User::factory()->count(16)->create();

    foreach ($users as $index => $user) {
        MaintenanceOfficer::create([
            'user_id' => $user->id,
            'employee_code' => sprintf('MNT-PAGE-DEFAULT-%02d', $index + 1),
        ]);
    }

    $officers = (new OfficerService)->paginateOfficers();

    expect($officers)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($officers->perPage())->toBe(15);
    expect($officers->count())->toBe(15);
    expect($officers->total())->toBe(16);
});

test('officer pagination uses the requested page size', function () {
    $users = User::factory()->count(8)->create();

    foreach ($users as $index => $user) {
        MaintenanceOfficer::create([
            'user_id' => $user->id,
            'employee_code' => sprintf('MNT-PAGE-CUSTOM-%02d', $index + 1),
        ]);
    }

    $officers = (new OfficerService)->paginateOfficers(5);

    expect($officers)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($officers->perPage())->toBe(5);
    expect($officers->count())->toBe(5);
    expect($officers->total())->toBe(8);
});

test('an officer is created and its input data is persisted', function () {
    $user = User::factory()->create();
    $data = [
        'user_id' => $user->id,
        'employee_code' => 'MNT-CREATE-01',
        'specialization_notes' => 'Electrical systems',
        'availability_status' => OfficerAvailability::Busy,
        'max_active_tasks' => 3,
        'is_active' => false,
    ];

    $officer = (new OfficerService)->createOfficer($data);

    expect($officer)->toBeInstanceOf(MaintenanceOfficer::class);
    expect($officer->employee_code)->toBe('MNT-CREATE-01');
    expect($officer->availability_status)->toBe(OfficerAvailability::Busy);
    $this->assertDatabaseHas('maintenance_officers', [
        'user_id' => $user->id,
        'employee_code' => 'MNT-CREATE-01',
        'specialization_notes' => 'Electrical systems',
        'availability_status' => OfficerAvailability::Busy->value,
        'max_active_tasks' => 3,
        'is_active' => false,
    ]);
});

test('an officer is updated and returned with the latest data', function () {
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-UPDATE-01',
        'specialization_notes' => 'Initial specialization',
        'availability_status' => OfficerAvailability::Available,
        'max_active_tasks' => 2,
        'is_active' => true,
    ]);

    $updated = (new OfficerService)->updateOfficer($officer, [
        'employee_code' => 'MNT-UPDATE-02',
        'specialization_notes' => 'Updated specialization',
        'availability_status' => OfficerAvailability::Busy,
        'max_active_tasks' => 4,
        'is_active' => false,
    ]);

    expect($updated->id)->toBe($officer->id);
    expect($updated->employee_code)->toBe('MNT-UPDATE-02');
    expect($updated->specialization_notes)->toBe('Updated specialization');
    expect($updated->availability_status)->toBe(OfficerAvailability::Busy);
    expect($updated->max_active_tasks)->toBe(4);
    expect($updated->is_active)->toBeFalse();
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'employee_code' => 'MNT-UPDATE-02',
        'specialization_notes' => 'Updated specialization',
        'availability_status' => OfficerAvailability::Busy->value,
        'max_active_tasks' => 4,
        'is_active' => false,
    ]);
});

test('an officer is soft deleted and remains available through trashed queries', function () {
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-DELETE-01',
    ]);

    $deleted = (new OfficerService)->deleteOfficer($officer);

    expect($deleted)->toBeTrue();
    expect(MaintenanceOfficer::find($officer->id))->toBeNull();
    expect(MaintenanceOfficer::withTrashed()->find($officer->id))->not->toBeNull();
    expect(MaintenanceOfficer::withTrashed()->find($officer->id)->trashed())->toBeTrue();
});

test('officer availability toggles as expected and persists', function (
    OfficerAvailability $initialAvailability,
    OfficerAvailability $expectedAvailability,
    string $employeeCode,
) {
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => $employeeCode,
        'availability_status' => $initialAvailability,
    ]);

    $updated = (new OfficerService)->toggleAvailability($officer);

    expect($updated)->toBeTrue();
    expect($officer->fresh()->availability_status)->toBe($expectedAvailability);
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'availability_status' => $expectedAvailability->value,
    ]);
})->with([
    'available officers become unavailable' => [
        OfficerAvailability::Available,
        OfficerAvailability::Unavailable,
        'MNT-TOGGLE-AVAILABLE',
    ],
    'unavailable officers become available' => [
        OfficerAvailability::Unavailable,
        OfficerAvailability::Available,
        'MNT-TOGGLE-UNAVAILABLE',
    ],
    'busy officers remain busy' => [
        OfficerAvailability::Busy,
        OfficerAvailability::Busy,
        'MNT-TOGGLE-BUSY',
    ],
]);
