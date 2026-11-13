<?php

use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Petugas Fasilitas',
        'email' => 'petugas@monifas.id',
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman master prioritas dan dialihkan ke login', function () {
    $this->get(route('reporting.report-priorities.index'))
        ->assertRedirect('/login');

    $this->get(route('reporting.report-priorities.create'))
        ->assertRedirect('/login');
});

test('pengguna terautentikasi dapat melihat halaman daftar prioritas dan matriks sla', function () {
    $priority = ReportPriority::factory()->emergency()->create();

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.index'))
        ->assertStatus(200)
        ->assertSee('Master Prioritas & SLA Laporan')
        ->assertSee('Ringkasan Matriks SLA Sesuai Tingkat Urgensi')
        ->assertSee($priority->name)
        ->assertSee('1 Jam');
});

test('pengguna dapat memfilter daftar prioritas berdasarkan level dan pencarian', function () {
    ReportPriority::factory()->emergency()->create(['name' => 'Darurat Listrik Padam']);
    ReportPriority::factory()->low()->create(['name' => 'Perawatan Berkala AC']);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.index', ['search' => 'Listrik']))
        ->assertStatus(200)
        ->assertViewHas('priorities', function ($priorities) {
            return $priorities->contains('name', 'Darurat Listrik Padam')
                && ! $priorities->contains('name', 'Perawatan Berkala AC');
        });

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.index', ['level' => PriorityLevel::Low->value]))
        ->assertStatus(200)
        ->assertViewHas('priorities', function ($priorities) {
            return $priorities->contains('name', 'Perawatan Berkala AC')
                && ! $priorities->contains('name', 'Darurat Listrik Padam');
        });
});

test('pengguna dapat membuka formulir tambah prioritas baru', function () {
    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.create'))
        ->assertStatus(200)
        ->assertSee('Tambah Prioritas Baru')
        ->assertSee('Target Respon Awal (Jam)')
        ->assertSee('Target Penyelesaian (Jam)');
});

test('pengguna dapat menyimpan data prioritas laporan baru', function () {
    $payload = [
        'name' => 'Prioritas Kritis Kebocoran',
        'level' => PriorityLevel::Emergency->value,
        'sla_response_hours' => 2,
        'sla_resolution_hours' => 6,
        'badge_variant' => 'danger',
        'sort_order' => 1,
        'description' => 'Kerusakan kebocoran air yang menggenangi server atau instalasi listrik.',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('reporting.report-priorities.store'), $payload);

    $response->assertRedirect(route('reporting.report-priorities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('report_priorities', [
        'name' => 'Prioritas Kritis Kebocoran',
        'level' => PriorityLevel::Emergency->value,
        'sla_response_hours' => 2,
        'sla_resolution_hours' => 6,
    ]);
});

test('validasi gagal jika target penyelesaian sla lebih kecil dari target respon', function () {
    $payload = [
        'name' => 'Invalid SLA Hours',
        'level' => PriorityLevel::High->value,
        'sla_response_hours' => 12,
        'sla_resolution_hours' => 4, // Tidak valid: resolution < response
        'badge_variant' => 'warning',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('reporting.report-priorities.store'), $payload);

    $response->assertSessionHasErrors(['sla_resolution_hours']);
});

test('pengguna dapat melihat detail prioritas laporan', function () {
    $priority = ReportPriority::factory()->high()->create();

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.show', $priority))
        ->assertStatus(200)
        ->assertSee($priority->name)
        ->assertSee('Batas Waktu Service Level Agreement (SLA)')
        ->assertSee((string) $priority->sla_response_hours);
});

test('pengguna dapat membuka halaman ubah data prioritas', function () {
    $priority = ReportPriority::factory()->create();

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('reporting.report-priorities.edit', $priority))
        ->assertStatus(200)
        ->assertSee('Ubah Data Prioritas')
        ->assertSee($priority->name);
});

test('pengguna dapat memperbarui data prioritas', function () {
    $priority = ReportPriority::factory()->create([
        'level' => PriorityLevel::Medium->value,
        'name' => 'Prioritas Sedang Lama',
        'sla_response_hours' => 12,
        'sla_resolution_hours' => 48,
    ]);

    $payload = [
        'name' => 'Prioritas Sedang Revisi',
        'level' => PriorityLevel::Medium->value,
        'sla_response_hours' => 8,
        'sla_resolution_hours' => 24,
        'badge_variant' => 'info',
        'sort_order' => 3,
        'description' => 'Deskripsi diperbarui',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)
        ->put(route('reporting.report-priorities.update', $priority), $payload);

    $response->assertRedirect(route('reporting.report-priorities.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('report_priorities', [
        'id' => $priority->id,
        'name' => 'Prioritas Sedang Revisi',
        'sla_response_hours' => 8,
        'sla_resolution_hours' => 24,
    ]);
});

test('pengguna dapat melakukan toggle status aktif prioritas', function () {
    $priority = ReportPriority::factory()->create(['is_active' => true]);

    $response = $this->actingAs($this->user)
        ->patch(route('reporting.report-priorities.toggle-status', $priority));

    $response->assertSessionHas('success');
    expect($priority->fresh()->is_active)->toBeFalse();
});

test('pengguna dapat menyinkronkan urutan prioritas standar otomatis', function () {
    $p1 = ReportPriority::factory()->create(['level' => PriorityLevel::Low->value, 'sort_order' => 1]);
    $p2 = ReportPriority::factory()->create(['level' => PriorityLevel::Emergency->value, 'sort_order' => 4]);

    $response = $this->actingAs($this->user)
        ->post(route('reporting.report-priorities.sync-order'));

    $response->assertRedirect(route('reporting.report-priorities.index'))
        ->assertSessionHas('success');

    expect($p1->fresh()->sort_order)->toBe(4)
        ->and($p2->fresh()->sort_order)->toBe(1);
});

test('pengguna dapat menghapus data prioritas secara soft delete', function () {
    $priority = ReportPriority::factory()->create();

    $response = $this->actingAs($this->user)
        ->delete(route('reporting.report-priorities.destroy', $priority));

    $response->assertRedirect(route('reporting.report-priorities.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('report_priorities', [
        'id' => $priority->id,
    ]);
});
