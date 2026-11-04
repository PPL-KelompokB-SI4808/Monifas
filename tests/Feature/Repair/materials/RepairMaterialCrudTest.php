<?php

use App\Models\Repair;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });

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

    $this->user = User::forceCreate([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $this->actingAs($this->user);

    $this->repair = Repair::create([]);
});

test('user dapat melihat halaman daftar material', function () {
    $this->get(route('repair.materials.index', $this->repair->id))
        ->assertOk();
});

test('user dapat melihat halaman tambah material', function () {
    $this->get(route('repair.materials.create', $this->repair->id))
        ->assertOk();
});

test('user dapat menambahkan material', function () {
    $this->post(route('repair.materials.store'), [
        'repair_id' => $this->repair->id,
        'materials' => [
            [
                'material_name' => 'Kabel',
                'material_type' => 'Elektronik',
                'unit' => 'Meter',
                'quantity' => 5,
                'unit_price' => 10000,
            ],
        ],
    ])->assertRedirect(route('repair.materials.index', $this->repair->id));

    $this->assertDatabaseHas('repair_materials', [
        'repair_id' => $this->repair->id,
        'material_name' => 'Kabel',
        'subtotal' => 50000,
    ]);
});

test('user dapat melihat halaman edit material', function () {
    $material = $this->repair->materials()->create([
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 2,
        'unit_price' => 10000,
        'subtotal' => 20000,
    ]);

    $this->get(route('repair.materials.edit', $material->id))
        ->assertOk();
});

test('user dapat memperbarui material', function () {
    $material = $this->repair->materials()->create([
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 2,
        'unit_price' => 10000,
        'subtotal' => 20000,
    ]);

    $this->put(route('repair.materials.update', $material->id), [
        'material_name' => 'Kabel Baru',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 3,
        'unit_price' => 15000,
    ])->assertRedirect(route('repair.materials.index', $this->repair->id));

    $this->assertDatabaseHas('repair_materials', [
        'id' => $material->id,
        'material_name' => 'Kabel Baru',
        'subtotal' => 45000,
    ]);
});

test('user dapat menghapus material', function () {
    $material = $this->repair->materials()->create([
        'material_name' => 'Kabel',
        'material_type' => 'Elektronik',
        'unit' => 'Meter',
        'quantity' => 2,
        'unit_price' => 10000,
        'subtotal' => 20000,
    ]);

    $this->delete(route('repair.materials.destroy', $material->id))
        ->assertRedirect(route('repair.materials.index', $this->repair->id));

    $this->assertDatabaseMissing('repair_materials', [
        'id' => $material->id,
    ]);
});

test('penambahan material ditolak jika quantity tidak valid', function () {
    $this->post(route('repair.materials.store'), [
        'repair_id' => $this->repair->id,
        'materials' => [
            [
                'material_name' => 'Kabel',
                'material_type' => 'Elektronik',
                'unit' => 'Meter',
                'quantity' => 0,
                'unit_price' => 10000,
            ],
        ],
    ])->assertSessionHasErrors('materials.0.quantity');
});
