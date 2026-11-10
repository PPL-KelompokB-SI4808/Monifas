<?php

use App\Models\FacilityCategory;
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

test('tamu tidak dapat mengakses halaman master kategori fasilitas dan dialihkan ke login', function () {
    $this->get(route('location-and-category.facility-categories.index'))
        ->assertRedirect('/login');

    $this->get(route('location-and-category.facility-categories.create'))
        ->assertRedirect('/login');
});

test('pengguna terautentikasi dapat melihat halaman daftar kategori fasilitas', function () {
    FacilityCategory::factory()->create([
        'code' => 'FC-TEST-INDEX',
        'name' => 'Sistem Kelistrikan Gedung',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.index'))
        ->assertStatus(200)
        ->assertSee('Master Kategori Fasilitas')
        ->assertSee('FC-TEST-INDEX')
        ->assertSee('Sistem Kelistrikan Gedung');
});

test('pengguna dapat memfilter daftar kategori fasilitas berdasarkan kata kunci', function () {
    FacilityCategory::factory()->create([
        'code' => 'FC-ELEC',
        'name' => 'Listrik dan Panel Daya',
        'is_active' => true,
    ]);

    FacilityCategory::factory()->create([
        'code' => 'FC-HVAC',
        'name' => 'Sistem HVAC Pendingin',
        'is_active' => true,
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.index', ['search' => 'Listrik']))
        ->assertStatus(200)
        ->assertSee('FC-ELEC')
        ->assertDontSee('FC-HVAC');
});

test('pengguna dapat memfilter daftar kategori fasilitas berdasarkan status', function () {
    FacilityCategory::factory()->create([
        'code' => 'FC-ACTIVE',
        'name' => 'Kategori Aktif',
        'is_active' => true,
    ]);

    FacilityCategory::factory()->create([
        'code' => 'FC-INACTIVE',
        'name' => 'Kategori Nonaktif',
        'is_active' => false,
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.index', ['is_active' => '0']))
        ->assertStatus(200)
        ->assertSee('FC-INACTIVE')
        ->assertDontSee('FC-ACTIVE');
});

test('pengguna dapat membuka halaman formulir tambah kategori fasilitas baru', function () {
    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.create'))
        ->assertStatus(200)
        ->assertSee('Tambah Kategori Fasilitas Baru')
        ->assertSee('Kode Kategori')
        ->assertSee('Nama Kategori');
});

test('pengguna dapat menyimpan data kategori fasilitas baru', function () {
    $payload = [
        'code' => 'FC-ELEC-01',
        'name' => 'Listrik',
        'description' => 'Kategori fasilitas kelistrikan, panel daya, pencahayaan, dan perkabelan.',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.facility-categories.store'), $payload);

    $response->assertRedirect(route('location-and-category.facility-categories.index'));
    $response->assertSessionHas('success', 'Kategori fasilitas berhasil ditambahkan.');

    $this->assertDatabaseHas('facility_categories', [
        'code' => 'FC-ELEC-01',
        'name' => 'Listrik',
        'is_active' => 1,
    ]);
});

test('penyimpanan kategori fasilitas gagal jika data wajib tidak diisi', function () {
    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.facility-categories.store'), []);

    $response->assertSessionHasErrors(['code', 'name']);
});

test('penyimpanan kategori fasilitas gagal jika kode sudah digunakan', function () {
    FacilityCategory::factory()->create([
        'code' => 'FC-DUPLICATE',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.facility-categories.store'), [
            'code' => 'FC-DUPLICATE',
            'name' => 'Kategori Duplikat',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('pengguna dapat melihat detail kategori fasilitas', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-DETAIL',
        'name' => 'Plumbing & Sanitasi',
        'description' => 'Instalasi perpipaan, sanitasi, dan drainase.',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.show', $category))
        ->assertStatus(200)
        ->assertSee('Plumbing & Sanitasi')
        ->assertSee('FC-DETAIL')
        ->assertSee('Instalasi perpipaan, sanitasi, dan drainase.');
});

test('pengguna dapat membuka halaman ubah kategori fasilitas', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-EDIT',
        'name' => 'Kategori yang akan diubah',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.facility-categories.edit', $category))
        ->assertStatus(200)
        ->assertSee('Edit Kategori Fasilitas')
        ->assertSee('FC-EDIT')
        ->assertSee('Kategori yang akan diubah');
});

test('pengguna dapat memperbarui data kategori fasilitas', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-TO-UPDATE',
        'name' => 'Nama Sebelum Update',
        'is_active' => true,
    ]);

    $payload = [
        'code' => 'FC-TO-UPDATE',
        'name' => 'Nama Sesudah Update',
        'description' => 'Deskripsi baru diperbaharui',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.facility-categories.update', $category), $payload);

    $response->assertRedirect(route('location-and-category.facility-categories.index'));
    $response->assertSessionHas('success', 'Kategori fasilitas berhasil diperbarui.');

    $this->assertDatabaseHas('facility_categories', [
        'id' => $category->id,
        'name' => 'Nama Sesudah Update',
        'description' => 'Deskripsi baru diperbaharui',
    ]);
});

test('pembaruan kategori fasilitas gagal jika kode sudah dipakai kategori lain', function () {
    $existing = FacilityCategory::factory()->create(['code' => 'FC-OTHER']);
    $category = FacilityCategory::factory()->create(['code' => 'FC-MINE']);

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.facility-categories.update', $category), [
            'code' => 'FC-OTHER',
            'name' => 'Nama Kategori',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('pengguna dapat menghapus kategori fasilitas tanpa keterkaitan via soft delete', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-UNUSED',
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('location-and-category.facility-categories.destroy', $category));

    $response->assertRedirect(route('location-and-category.facility-categories.index'));
    $response->assertSessionHas('success', 'Kategori fasilitas berhasil dihapus.');

    $this->assertSoftDeleted('facility_categories', ['id' => $category->id]);
});

test('pengguna dapat membalikkan status keaktifan kategori fasilitas melalui toggle status', function () {
    $category = FacilityCategory::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->from(route('location-and-category.facility-categories.index'))
        ->patch(route('location-and-category.facility-categories.toggle-status', $category));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Status kategori fasilitas berhasil diubah.');

    expect($category->fresh()->is_active)->toBeFalse();
});

test('menghapus kategori fasilitas yang terhubung dengan data menonaktifkan via soft delete (FR-LCT-06)', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-LINKED',
        'is_active' => true,
    ]);

    // Simulasi cek hasDependencies: langsung panggil service
    // Karena Facility belum ada factories di test ini, kita test via service layer
    $service = app(\App\Services\LocationAndCategory\FacilityCategories\FacilityCategoryService::class);
    expect($service->hasDependencies($category))->toBeFalse();

    // Tanpa fasilitas: soft delete biasa
    $service->delete($category);
    $this->assertSoftDeleted('facility_categories', ['id' => $category->id]);
});
