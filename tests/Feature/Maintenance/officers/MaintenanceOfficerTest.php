<?php

use App\Enums\OfficerAvailability;
use App\Models\MaintenanceOfficer;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('officer availability enum uses the expected backed values', function () {
    $availabilityValues = array_map(
        fn (OfficerAvailability $availability): string => $availability->value,
        OfficerAvailability::cases(),
    );

    expect($availabilityValues)->toBe(['available', 'busy', 'unavailable']);
});

test('officer stores its availability as an enum and belongs to a user', function () {
    $user = User::factory()->create();

    $officer = MaintenanceOfficer::create([
        'user_id' => $user->id,
        'employee_code' => 'MNT-001',
        'availability_status' => OfficerAvailability::Busy,
    ]);

    expect($officer->fresh()->availability_status)->toBe(OfficerAvailability::Busy);
    expect($officer->user())->toBeInstanceOf(BelongsTo::class);
    expect($officer->user->is($user))->toBeTrue();
});

test('officer can be soft deleted', function () {
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-002',
    ]);

    $officer->refresh();

    expect($officer->availability_status)->toBe(OfficerAvailability::Available);
    expect($officer->max_active_tasks)->toBe(5);
    expect($officer->is_active)->toBeTrue();

    $officer->delete();

    expect(MaintenanceOfficer::find($officer->id))->toBeNull();
    expect(MaintenanceOfficer::withTrashed()->find($officer->id))->not->toBeNull();
    expect($officer->trashed())->toBeTrue();
});

test('officer requires a unique user and employee code', function () {
    $user = User::factory()->create();

    MaintenanceOfficer::create([
        'user_id' => $user->id,
        'employee_code' => 'MNT-003',
    ]);

    expect(fn () => MaintenanceOfficer::create([
        'user_id' => $user->id,
        'employee_code' => 'MNT-004',
    ]))->toThrow(QueryException::class);

    expect(fn () => MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-003',
    ]))->toThrow(QueryException::class);
});

test('deleting the associated user removes the officer', function () {
    $user = User::factory()->create();
    $officer = MaintenanceOfficer::create([
        'user_id' => $user->id,
        'employee_code' => 'MNT-005',
    ]);

    $user->forceDelete();

    expect(MaintenanceOfficer::withTrashed()->find($officer->id))->toBeNull();
});
