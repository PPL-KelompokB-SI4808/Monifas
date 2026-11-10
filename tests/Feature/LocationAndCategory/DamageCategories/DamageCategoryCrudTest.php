<?php

use App\Models\DamageCategory;
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

test('tamu tidak dapat mengakses halaman master kategori kerusakan dan dialihkan ke login', function () {
    $this->get(route('location-and-category.damage-categories.index'))
        ->assertRedirect('/login');

    $this->get(route('location-and-category.damage-categories.create'))
        ->assertRedirect('/login');
});

test('pengguna terautentikasi dapat melihat halaman daftar kategori kerusakan', function () {
    DamageCategory::factory()->create([
        'code' => 'DC-TEST-INDEX',
        'name' => 'Korsleting Listrik',
        'severity_hint' => 'Memerlukan Penanganan Segera',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.index'))
        ->assertStatus(200)
        ->assertSee('Master Kategori Kerusakan')
        ->assertSee('DC-TEST-INDEX')
        ->assertSee('Korsleting Listrik');
});

test('pengguna dapat memfilter daftar kategori kerusakan berdasarkan kata kunci', function () {
    DamageCategory::factory()->create([
        'code' => 'DC-ELEC',
        'name' => 'Korsleting Listrik dan Panel',
        'is_active' => true,
    ]);

    DamageCategory::factory()->create([
        'code' => 'DC-PIPE',
        'name' => 'Kebocoran Pipa Utama',
        'is_active' => true,
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.index', ['search' => 'Korsleting']))
        ->assertStatus(200)
        ->assertSee('DC-ELEC')
        ->assertDontSee('DC-PIPE');
});

test('pengguna dapat memfilter daftar kategori kerusakan berdasarkan status', function () {
    DamageCategory::factory()->create([
        'code' => 'DC-ACTIVE',
        'name' => 'Kerusakan Aktif',
        'is_active' => true,
    ]);

    DamageCategory::factory()->create([
        'code' => 'DC-INACTIVE',
        'name' => 'Kerusakan Nonaktif',
        'is_active' => false,
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.index', ['is_active' => '1']))
        ->assertStatus(200)
        ->assertSee('DC-ACTIVE')
        ->assertDontSee('DC-INACTIVE');
});

test('pengguna dapat membuka halaman formulir tambah kategori kerusakan baru', function () {
    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.create'))
        ->assertStatus(200)
        ->assertSee('Tambah Kategori Kerusakan Baru')
        ->assertSee('Kode Kategori Kerusakan')
        ->assertSee('Nama Kategori Kerusakan');
});

test('pengguna dapat menyimpan data kategori kerusakan baru', function () {
    $payload = [
        'code' => 'DC-ELEC-01',
        'name' => 'Korsleting Listrik',
        'severity_hint' => 'Memerlukan Penanganan Segera',
        'description' => 'Kerusakan arus pendek pada instalasi kelistrikan gedung.',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.damage-categories.store'), $payload);

    $response->assertRedirect(route('location-and-category.damage-categories.index'));
    $response->assertSessionHas('success', 'Kategori kerusakan berhasil ditambahkan.');

    $this->assertDatabaseHas('damage_categories', [
        'code' => 'DC-ELEC-01',
        'name' => 'Korsleting Listrik',
        'severity_hint' => 'Memerlukan Penanganan Segera',
        'is_active' => 1,
    ]);
});

test('penyimpanan kategori kerusakan gagal jika data wajib tidak diisi', function () {
    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.damage-categories.store'), []);

    $response->assertSessionHasErrors(['code', 'name']);
});

test('penyimpanan kategori kerusakan gagal jika kode sudah digunakan', function () {
    DamageCategory::factory()->create([
        'code' => 'DC-DUPLICATE',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.damage-categories.store'), [
            'code' => 'DC-DUPLICATE',
            'name' => 'Kategori Kerusakan Duplikat',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('validasi severity_hint tidak boleh melebihi 50 karakter', function () {
    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.damage-categories.store'), [
            'code' => 'DC-VAL-01',
            'name' => 'Kategori Valid',
            'severity_hint' => str_repeat('A', 51),
        ]);

    $response->assertSessionHasErrors(['severity_hint']);
});

test('pengguna dapat melihat detail kategori kerusakan', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-DETAIL',
        'name' => 'Kebocoran Atap Parah',
        'severity_hint' => 'Memerlukan Penanganan Segera',
        'description' => 'Kebocoran pada atap yang berdampak pada beberapa ruangan.',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.show', $category))
        ->assertStatus(200)
        ->assertSee('Kebocoran Atap Parah')
        ->assertSee('DC-DETAIL')
        ->assertSee('Memerlukan Penanganan Segera');
});

test('pengguna dapat membuka halaman ubah kategori kerusakan', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-EDIT',
        'name' => 'Kategori yang akan diubah',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.damage-categories.edit', $category))
        ->assertStatus(200)
        ->assertSee('Edit Kategori Kerusakan')
        ->assertSee('DC-EDIT')
        ->assertSee('Kategori yang akan diubah');
});

test('pengguna dapat memperbarui data kategori kerusakan', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-TO-UPDATE',
        'name' => 'Nama Sebelum Update',
        'is_active' => true,
    ]);

    $payload = [
        'code' => 'DC-TO-UPDATE',
        'name' => 'Nama Sesudah Update',
        'severity_hint' => 'Penanganan Standar',
        'description' => 'Deskripsi diperbaharui',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.damage-categories.update', $category), $payload);

    $response->assertRedirect(route('location-and-category.damage-categories.index'));
    $response->assertSessionHas('success', 'Kategori kerusakan berhasil diperbarui.');

    $this->assertDatabaseHas('damage_categories', [
        'id' => $category->id,
        'name' => 'Nama Sesudah Update',
        'severity_hint' => 'Penanganan Standar',
        'description' => 'Deskripsi diperbaharui',
    ]);
});

test('pembaruan kategori kerusakan gagal jika kode sudah dipakai kategori lain', function () {
    $existing = DamageCategory::factory()->create(['code' => 'DC-OTHER']);
    $category = DamageCategory::factory()->create(['code' => 'DC-MINE']);

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.damage-categories.update', $category), [
            'code' => 'DC-OTHER',
            'name' => 'Nama Kategori',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('pengguna dapat menghapus kategori kerusakan tanpa keterkaitan via soft delete', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-UNUSED',
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('location-and-category.damage-categories.destroy', $category));

    $response->assertRedirect(route('location-and-category.damage-categories.index'));
    $response->assertSessionHas('success', 'Kategori kerusakan berhasil dihapus.');

    $this->assertSoftDeleted('damage_categories', ['id' => $category->id]);
});

test('pengguna dapat membalikkan status keaktifan kategori kerusakan melalui toggle status', function () {
    $category = DamageCategory::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->from(route('location-and-category.damage-categories.index'))
        ->patch(route('location-and-category.damage-categories.toggle-status', $category));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Status kategori kerusakan berhasil diubah.');

    expect($category->fresh()->is_active)->toBeFalse();
});

test('kategori kerusakan tanpa laporan terkait dihapus via soft delete (FR-LCT-06)', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-SAFE-DEL',
        'is_active' => true,
    ]);

    $service = app(\App\Services\LocationAndCategory\DamageCategories\DamageCategoryService::class);

    // Tanpa tabel reports atau tanpa laporan terkait: hasDependencies harus false
    expect($service->hasDependencies($category))->toBeFalse();

    $service->delete($category);
    $this->assertSoftDeleted('damage_categories', ['id' => $category->id]);
});
