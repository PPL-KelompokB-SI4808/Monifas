<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
        'description' => 'Akses penuh ke seluruh sistem',
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Monifas',
        'email' => 'admin@monifas.id',
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman manajemen peran', function () {
    $response = $this->get(route('users-and-access.roles.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-admin tidak memiliki otorisasi mengakses manajemen peran (403)', function () {
    $technicianRole = Role::create([
        'name' => 'Teknisi',
        'slug' => UserRole::Technician->value,
    ]);

    $technician = User::factory()->create([
        'role_id' => $technicianRole->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($technician)->get(route('users-and-access.roles.index'));

    $response->assertStatus(403);
});

test('admin dapat melihat daftar peran pada halaman index', function () {
    Role::create([
        'name' => 'Kepala Ruangan',
        'slug' => 'kepala_ruangan',
        'description' => 'Penanggung jawab ruangan',
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.roles.index'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Peran');
    $response->assertSee('Administrator');
    $response->assertSee('Kepala Ruangan');
});

test('admin dapat melihat halaman pembuatan peran baru', function () {
    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.roles.create'));

    $response->assertStatus(200);
    $response->assertSee('Tambah Peran Baru');
    $response->assertSee('Nama Peran');
    $response->assertSee('Identifier / Slug');
});

test('admin berhasil menyimpan peran baru dengan data valid', function () {
    $payload = [
        'name' => 'Koordinator Lapangan',
        'slug' => 'koordinator_lapangan',
        'description' => 'Memimpin teknisi di lapangan',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.roles.store'), $payload);

    $response->assertRedirect(route('users-and-access.roles.index'));
    $response->assertSessionHas('status', 'Peran baru berhasil ditambahkan.');

    $created = Role::where('slug', 'koordinator_lapangan')->first();
    expect($created)->not->toBeNull();
    expect($created->name)->toBe('Koordinator Lapangan');
    expect($created->description)->toBe('Memimpin teknisi di lapangan');
});

test('validasi pembuatan peran gagal jika nama atau slug tidak valid', function () {
    // Missing required fields & invalid slug format (contains spaces or uppercase)
    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.roles.store'), [
            'name' => '',
            'slug' => 'SLUG DENGAN SPASI!',
            'description' => 'Deskripsi',
        ]);

    $response->assertSessionHasErrors(['name', 'slug']);

    // Duplicate slug
    Role::create([
        'name' => 'Auditor',
        'slug' => 'auditor',
    ]);

    $dupResponse = $this->actingAs($this->admin)
        ->post(route('users-and-access.roles.store'), [
            'name' => 'Auditor Khusus',
            'slug' => 'auditor',
        ]);

    $dupResponse->assertSessionHasErrors(['slug']);
});

test('admin dapat melihat form edit peran', function () {
    $role = Role::create([
        'name' => 'Staf Logistik',
        'slug' => 'staf_logistik',
        'description' => 'Pengelola material perbaikan',
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.roles.edit', $role));

    $response->assertStatus(200);
    $response->assertSee('Edit Peran: Staf Logistik');
    $response->assertSee('staf_logistik');
});

test('admin berhasil memperbarui data peran', function () {
    $role = Role::create([
        'name' => 'Nama Awal',
        'slug' => 'slug_awal',
        'description' => 'Deskripsi awal',
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('users-and-access.roles.update', $role), [
            'name' => 'Nama Direvisi',
            'slug' => 'slug_awal',
            'description' => 'Deskripsi yang diperbarui',
        ]);

    $response->assertRedirect(route('users-and-access.roles.index'));
    $response->assertSessionHas('status', 'Data peran berhasil diperbarui.');

    $refreshed = $role->fresh();
    expect($refreshed->name)->toBe('Nama Direvisi');
    expect($refreshed->description)->toBe('Deskripsi yang diperbarui');
});

test('admin berhasil menghapus peran yang belum memiliki pengguna', function () {
    $role = Role::create([
        'name' => 'Peran Uji Hapus',
        'slug' => 'peran_uji_hapus',
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('users-and-access.roles.destroy', $role));

    $response->assertRedirect(route('users-and-access.roles.index'));
    $response->assertSessionHas('status', 'Peran berhasil dihapus.');

    expect(Role::find($role->id))->toBeNull();
});

test('admin gagal menghapus peran yang masih memiliki pengguna terdaftar', function () {
    $role = Role::create([
        'name' => 'Peran Aktif',
        'slug' => 'peran_aktif',
    ]);

    User::factory()->create([
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('users-and-access.roles.destroy', $role));

    $response->assertRedirect(route('users-and-access.roles.index'));
    $response->assertSessionHasErrors(['error']);

    expect(Role::find($role->id))->not->toBeNull();
});
