<?php

use App\Models\Repair;
use App\Services\Repair\repairs\RepairService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('model repair dapat menyimpan atribut pelaksanaan perbaikan', function () {
    $repair = new Repair([
        'technical_action' => 'Penggantian komponen',
        'repair_result' => 'Komponen berhasil diganti',
        'resulting_facility_condition' => 'baik',
        'status' => 'completed',
    ]);

    expect($repair->technical_action)->toBe('Penggantian komponen');
    expect($repair->repair_result)->toBe('Komponen berhasil diganti');
    expect($repair->resulting_facility_condition)->toBe('baik');
});

test('service repair dapat dibuat melalui dependency injection Laravel', function () {
    $service = app(RepairService::class);

    expect($service)->toBeInstanceOf(RepairService::class);
});

test('repair yang belum selesai tidak memenuhi syarat penutupan', function () {
    $repair = new Repair([
        'status' => 'in_progress',
        'ended_at' => null,
        'repair_result' => null,
    ]);

    expect($repair->isCompleteForClosure())->toBeFalse();
});
