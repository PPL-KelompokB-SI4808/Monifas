<?php

use App\Models\Repair;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('tamu tidak dapat mengakses riwayat perbaikan', function () {
    $response = $this->get(route('repair.repairs.index'));

    $response->assertRedirect('/login');
});

test('pengguna yang login dapat melihat halaman riwayat perbaikan', function () {
    $response = $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('repair.repairs.index'));

    $response->assertOk();
    $response->assertSee('Riwayat Perbaikan');
});

test('pengguna yang login dapat membuka halaman tambah perbaikan', function () {
    $response = $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('repair.repairs.create'));

    $response->assertOk();
    $response->assertSee('Tambah Perbaikan');
});

test('data perbaikan memiliki atribut yang sesuai setelah dibuat', function () {
    $repair = new Repair([
        'assignment_id' => 1,
        'report_id' => null,
        'facility_id' => 1,
        'officer_id' => 1,
        'started_at' => now(),
        'ended_at' => null,
        'technical_action' => 'Pemeriksaan awal',
        'repair_result' => null,
        'resulting_facility_condition' => 'baik',
        'status' => 'draft',
    ]);

    expect($repair->technical_action)->toBe('Pemeriksaan awal');
    expect($repair->facility_id)->toBe(1);
});