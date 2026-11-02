<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAddress;
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

    $this->regularUser = User::factory()->create([
        'name' => 'Pengguna Reguler',
        'email' => 'user@monifas.id',
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman manajemen alamat pengguna', function () {
    $response = $this->get(route('users-and-access.user-addresses.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-admin tidak dapat mengakses manajemen alamat pengguna (403)', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('users-and-access.user-addresses.index'));

    $response->assertStatus(403);
});

test('admin dapat melihat halaman daftar alamat pengguna', function () {
    $address = UserAddress::factory()->create([
        'user_id' => $this->regularUser->id,
        'label' => 'Ruang Dosen Gedung A',
        'address_detail' => 'Lantai 2 No. 201',
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.user-addresses.index'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Alamat Pengguna');
    $response->assertSee('Ruang Dosen Gedung A');
    $response->assertSee('Pengguna Reguler');
});

test('admin dapat memfilter daftar alamat pengguna berdasarkan user_id dan search', function () {
    $userA = User::factory()->create(['name' => 'Budi Santoso']);
    $userB = User::factory()->create(['name' => 'Siti Nurhaliza']);

    $addrA = UserAddress::factory()->create([
        'user_id' => $userA->id,
        'label' => 'Kantor Budi',
    ]);

    $addrB = UserAddress::factory()->create([
        'user_id' => $userB->id,
        'label' => 'Rumah Siti',
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.user-addresses.index', ['search' => 'Budi']));

    $response->assertStatus(200);
    $response->assertSee('Kantor Budi');
    $response->assertDontSee('Rumah Siti');
});

test('admin dapat melihat halaman tambah alamat pengguna', function () {
    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.user-addresses.create'));

    $response->assertStatus(200);
    $response->assertSee('Tambah Alamat Pengguna');
    $response->assertSee('Pemilik Alamat (Pengguna)');
});

test('admin berhasil menambahkan alamat baru', function () {
    $payload = [
        'user_id' => $this->regularUser->id,
        'label' => 'Workshop Elektronika',
        'building_or_unit' => 'Gedung C Lantai 1',
        'address_detail' => 'Kavling Kampus Barat No. 5',
        'contact_phone' => '081298765432',
        'postal_code' => '15412',
        'is_primary' => '1',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.user-addresses.store'), $payload);

    $response->assertRedirect(route('users-and-access.user-addresses.index'));
    $response->assertSessionHas('status', 'Alamat pengguna berhasil ditambahkan.');

    $created = UserAddress::where('label', 'Workshop Elektronika')->first();
    expect($created)->not->toBeNull();
    expect($created->user_id)->toBe($this->regularUser->id);
    expect($created->building_or_unit)->toBe('Gedung C Lantai 1');
    expect($created->is_primary)->toBeTrue();
});

test('validasi gagal jika field yang wajib tidak diisi pada pembuatan alamat', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.user-addresses.store'), [
            'label' => '',
            'address_detail' => '',
        ]);

    $response->assertSessionHasErrors(['user_id', 'label', 'address_detail']);
});

test('validasi nomor telepon dan kode pos gagal jika format bukan angka atau digit tidak sesuai', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.user-addresses.store'), [
            'user_id' => $this->regularUser->id,
            'label' => 'Alamat Salah',
            'address_detail' => 'Jl. Pengujian No. 12',
            'contact_phone' => 'bukan-nomor-telepon-abc',
            'postal_code' => '1234', // Kurang dari 5 digit
        ]);

    $response->assertSessionHasErrors(['contact_phone', 'postal_code']);

    // Cek jika kode pos mengandung huruf
    $response2 = $this->actingAs($this->admin)
        ->post(route('users-and-access.user-addresses.store'), [
            'user_id' => $this->regularUser->id,
            'label' => 'Alamat Salah 2',
            'address_detail' => 'Jl. Pengujian No. 13',
            'postal_code' => '12A45',
        ]);

    $response2->assertSessionHasErrors(['postal_code']);
});

test('admin dapat melihat halaman edit alamat pengguna', function () {
    $address = UserAddress::factory()->create([
        'user_id' => $this->regularUser->id,
        'label' => 'Alamat Pengujian Edit',
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.user-addresses.edit', $address));

    $response->assertStatus(200);
    $response->assertSee('Edit Alamat Pengguna');
    $response->assertSee('Alamat Pengujian Edit');
});

test('admin berhasil memperbarui alamat pengguna', function () {
    $address = UserAddress::factory()->create([
        'user_id' => $this->regularUser->id,
        'label' => 'Alamat Sebelum Diedit',
        'is_primary' => false,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('users-and-access.user-addresses.update', $address), [
            'user_id' => $this->regularUser->id,
            'label' => 'Alamat Setelah Diedit',
            'building_or_unit' => 'Gedung Baru',
            'address_detail' => 'Jl. Baru No. 88',
            'contact_phone' => '081111222333',
            'postal_code' => '40123',
            'is_primary' => '1',
        ]);

    $response->assertRedirect(route('users-and-access.user-addresses.index'));
    $response->assertSessionHas('status', 'Alamat pengguna berhasil diperbarui.');

    $refreshed = $address->fresh();
    expect($refreshed->label)->toBe('Alamat Setelah Diedit');
    expect($refreshed->building_or_unit)->toBe('Gedung Baru');
    expect($refreshed->is_primary)->toBeTrue();
});

test('admin berhasil menghapus alamat pengguna', function () {
    $address = UserAddress::factory()->create([
        'user_id' => $this->regularUser->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('users-and-access.user-addresses.destroy', $address));

    $response->assertRedirect(route('users-and-access.user-addresses.index'));
    $response->assertSessionHas('status', 'Alamat pengguna berhasil dihapus.');

    expect(UserAddress::find($address->id))->toBeNull();
});
