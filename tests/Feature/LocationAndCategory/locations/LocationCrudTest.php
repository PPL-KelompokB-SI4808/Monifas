<?php

use App\Models\Location;
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

test('tamu tidak dapat mengakses halaman master lokasi dan dialihkan ke login', function () {
    $this->get(route('location-and-category.locations.index'))
        ->assertRedirect('/login');

    $this->get(route('location-and-category.locations.create'))
        ->assertRedirect('/login');
});

test('pengguna terautentikasi dapat melihat halaman daftar lokasi', function () {
    $loc1 = Location::factory()->create([
        'code' => 'LOC-TEST-INDEX',
        'name' => 'Kawasan Barat Utama',
        'region' => 'Bandung',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.locations.index'))
        ->assertStatus(200)
        ->assertSee('Master Lokasi Kawasan')
        ->assertSee('LOC-TEST-INDEX')
        ->assertSee('Kawasan Barat Utama');
});

test('pengguna dapat memfilter daftar lokasi berdasarkan kata kunci dan status', function () {
    Location::factory()->create([
        'code' => 'LOC-ALPHA',
        'name' => 'Kawasan Alpha Bandung',
        'region' => 'Bandung',
        'is_active' => true,
    ]);

    Location::factory()->create([
        'code' => 'LOC-BETA',
        'name' => 'Kawasan Beta Jakarta',
        'region' => 'Jakarta',
        'is_active' => false,
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.locations.index', ['search' => 'Alpha']))
        ->assertStatus(200)
        ->assertSee('LOC-ALPHA')
        ->assertDontSee('LOC-BETA');
});

test('pengguna dapat membuka halaman formulir tambah lokasi baru', function () {
    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.locations.create'))
        ->assertStatus(200)
        ->assertSee('Tambah Lokasi Baru')
        ->assertSee('Kode Lokasi')
        ->assertSee('Nama Lokasi');
});

test('pengguna dapat menyimpan data lokasi induk baru', function () {
    $payload = [
        'code' => 'LOC-KPS-01',
        'name' => 'Kampus Induk Bandung',
        'region' => 'Bandung',
        'address' => 'Jl. Telekomunikasi Terusan Buah Batu',
        'description' => 'Area pusat rektorat dan gedung kuliah',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.locations.store'), $payload);

    $response->assertRedirect(route('location-and-category.locations.index'));
    $response->assertSessionHas('success', 'Lokasi berhasil ditambahkan.');

    $this->assertDatabaseHas('locations', [
        'code' => 'LOC-KPS-01',
        'name' => 'Kampus Induk Bandung',
        'region' => 'Bandung',
        'parent_id' => null,
        'is_active' => 1,
    ]);
});

test('pengguna dapat menyimpan sub-lokasi yang menginduk ke lokasi lain', function () {
    $parent = Location::factory()->create([
        'code' => 'LOC-PARENT-01',
        'name' => 'Kawasan Utama',
    ]);

    $payload = [
        'parent_id' => $parent->id,
        'code' => 'LOC-SUB-01',
        'name' => 'Zona Asrama Mahasiswa',
        'region' => 'Bandung',
        'address' => 'Komplek Asrama Telkom University',
        'description' => 'Sub-kawasan perumahan mahasiswa',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.locations.store'), $payload);

    $response->assertRedirect(route('location-and-category.locations.index'));

    $this->assertDatabaseHas('locations', [
        'code' => 'LOC-SUB-01',
        'name' => 'Zona Asrama Mahasiswa',
        'parent_id' => $parent->id,
    ]);
});

test('penyimpanan lokasi gagal jika data wajib tidak diisi', function () {
    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.locations.store'), []);

    $response->assertSessionHasErrors(['code', 'name', 'region', 'address']);
});

test('penyimpanan lokasi gagal jika kode lokasi sudah digunakan', function () {
    Location::factory()->create([
        'code' => 'LOC-DUPLICATE',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('location-and-category.locations.store'), [
            'code' => 'LOC-DUPLICATE',
            'name' => 'Lokasi Duplikat',
            'region' => 'Bandung',
            'address' => 'Jl. Bebas',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('pengguna dapat melihat detail lokasi beserta relasi sub-kawasan dan gedung', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-DETAIL',
        'name' => 'Kawasan Eksplorasi',
        'address' => 'Jl. Eksplorasi No. 10',
    ]);

    $child = Location::factory()->create([
        'parent_id' => $location->id,
        'code' => 'LOC-SUB-EXP',
        'name' => 'Sub Kawasan Eksplorasi',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.locations.show', $location))
        ->assertStatus(200)
        ->assertSee('Kawasan Eksplorasi')
        ->assertSee('LOC-DETAIL')
        ->assertSee('Sub Kawasan Eksplorasi');
});

test('pengguna dapat membuka halaman ubah lokasi', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-EDIT',
        'name' => 'Lokasi yang akan diubah',
    ]);

    $this->withoutVite()
        ->actingAs($this->user)
        ->get(route('location-and-category.locations.edit', $location))
        ->assertStatus(200)
        ->assertSee('Ubah Lokasi')
        ->assertSee('LOC-EDIT')
        ->assertSee('Lokasi yang akan diubah');
});

test('pengguna dapat memperbarui data lokasi yang ada', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-TO-UPDATE',
        'name' => 'Nama Sebelum Update',
        'region' => 'Bandung',
        'address' => 'Alamat Awal',
        'is_active' => true,
    ]);

    $payload = [
        'code' => 'LOC-TO-UPDATE',
        'name' => 'Nama Sesudah Update Sukses',
        'region' => 'Sumedang',
        'address' => 'Alamat Baru Lengkap',
        'description' => 'Deskripsi diperbarui',
        'is_active' => 1,
    ];

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.locations.update', $location), $payload);

    $response->assertRedirect(route('location-and-category.locations.index'));
    $response->assertSessionHas('success', 'Lokasi berhasil diperbarui.');

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'name' => 'Nama Sesudah Update Sukses',
        'region' => 'Sumedang',
        'address' => 'Alamat Baru Lengkap',
    ]);
});

test('pembaruan lokasi gagal jika memilih dirinya sendiri sebagai parent', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-SELF',
        'name' => 'Lokasi Mandiri',
        'region' => 'Bandung',
        'address' => 'Jl. Mandiri',
    ]);

    $response = $this->actingAs($this->user)
        ->put(route('location-and-category.locations.update', $location), [
            'parent_id' => $location->id,
            'code' => 'LOC-SELF',
            'name' => 'Lokasi Mandiri',
            'region' => 'Bandung',
            'address' => 'Jl. Mandiri',
        ]);

    $response->assertSessionHasErrors(['parent_id']);
});

test('pengguna dapat menghapus lokasi tanpa keterkaitan secara aman (soft delete)', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-UNUSED',
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('location-and-category.locations.destroy', $location));

    $response->assertRedirect(route('location-and-category.locations.index'));
    $response->assertSessionHas('success', 'Lokasi berhasil dihapus.');

    $this->assertSoftDeleted('locations', ['id' => $location->id]);
});

test('menghapus lokasi dengan sub-kawasan atau gedung menonaktifkan lokasi alih-alih hard delete', function () {
    $parent = Location::factory()->create([
        'code' => 'LOC-CASCADE-CHECK',
        'is_active' => true,
    ]);

    Location::factory()->create([
        'parent_id' => $parent->id,
        'code' => 'LOC-CASCADE-CHILD',
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('location-and-category.locations.destroy', $parent));

    $response->assertRedirect(route('location-and-category.locations.index'));
    $response->assertSessionHas('success', 'Lokasi dinonaktifkan karena memiliki sub-lokasi, gedung, atau fasilitas terkait.');

    $this->assertDatabaseHas('locations', [
        'id' => $parent->id,
        'is_active' => 0,
        'deleted_at' => null,
    ]);
});

test('pengguna dapat membalikkan status keaktifan lokasi melalui toggle status', function () {
    $location = Location::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->from(route('location-and-category.locations.index'))
        ->patch(route('location-and-category.locations.toggle-status', $location));

    $response->assertRedirect(route('location-and-category.locations.index'));
    $response->assertSessionHas('success', 'Status keaktifan lokasi berhasil diubah.');

    expect($location->fresh()->is_active)->toBeFalse();
});
