
<?php

use App\Models\Repair;
use App\Models\RepairMaterial;
use App\Services\Repair\materials\RepairMaterialService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Schema::create('repairs', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });

    Schema::create('repair_materials', function (Blueprint $table) {
        $table->id();
        $table->foreignId('repair_id')
            ->constrained('repairs')
            ->cascadeOnDelete();
        $table->string('material_name', 150);
        $table->string('material_type', 100);
        $table->string('unit', 30);
        $table->decimal('quantity', 10, 2);
        $table->decimal('unit_price', 15, 2)->default(0);
        $table->decimal('subtotal', 15, 2)->default(0);
        $table->text('notes')->nullable();
        $table->timestamps();
    });
});

test('repair material dapat menyimpan atribut material', function () {
    $material = new RepairMaterial([
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 5,
        'unit_price' => 10000,
        'subtotal' => 50000,
    ]);

    expect($material->material_name)->toBe('Kabel')
        ->and($material->material_type)->toBe('Elektronik')
        ->and($material->unit)->toBe('Meter')
        ->and((float) $material->quantity)->toBe(5.0)
        ->and((float) $material->unit_price)->toBe(10000.0)
        ->and((float) $material->subtotal)->toBe(50000.0);
});

test('repair material service dapat dibuat melalui dependency injection Laravel', function () {
    $service = app(RepairMaterialService::class);

    expect($service)->toBeInstanceOf(RepairMaterialService::class);
});

test('it calculates subtotal when storing materials', function () {
    $repair = Repair::create([]);
    $service = app(RepairMaterialService::class);

    $materials = $service->store([
        'repair_id' => $repair->id,
        'materials' => [
            [
                'material_name' => 'Kabel',
                'material_type' => 'Elektronik',
                'unit' => 'Meter',
                'quantity' => 5,
                'unit_price' => 10000,
            ],
        ],
    ]);

    expect($materials)->toHaveCount(1)
        ->and((float) $materials->first()->subtotal)->toBe(50000.0);
});

test('it calculates total material cost for a repair', function () {
    $repair = Repair::create([]);

    $repair->materials()->createMany([
        [
            'material_name' => 'Kabel',
            'material_type' => 'Elektronik',
            'unit' => 'Meter',
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
        ],
        [
            'material_name' => 'Baut',
            'material_type' => 'Hardware',
            'unit' => 'Pcs',
            'quantity' => 5,
            'unit_price' => 2000,
            'subtotal' => 10000,
        ],
    ]);

    $service = app(RepairMaterialService::class);

    expect($service->calculateMaterialTotal($repair))->toBe(30000.0);
});

test('it updates material subtotal when quantity or price changes', function () {
    $repair = Repair::create([]);

    $material = $repair->materials()->create([
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 2,
        'unit_price' => 10000,
        'subtotal' => 20000,
    ]);

    $updated = app(RepairMaterialService::class)->update($material, [
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 3,
        'unit_price' => 15000,
    ]);

    expect((float) $updated->subtotal)->toBe(45000.0);
});
