<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
        'description' => 'Akses penuh ke seluruh sistem',
    ]);

    $this->technicianRole = Role::create([
        'name' => 'Teknisi Lapangan',
        'slug' => UserRole::Technician->value,
        'description' => 'Petugas pemeliharaan fisik',
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Monifas',
        'email' => 'admin@monifas.id',
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman manajemen pengguna', function () {
    $response = $this->get(route('users-and-access.users.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-admin ditolak dengan status 403', function () {
    $technician = User::factory()->create([
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($technician)->get(route('users-and-access.users.index'));

    $response->assertStatus(403);
});

test('pengguna admin yang berstatus nonaktif ditolak dengan status 403', function () {
    $inactiveAdmin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'is_active' => false,
    ]);

    $response = $this->actingAs($inactiveAdmin)->get(route('users-and-access.users.index'));

    $response->assertStatus(403);
});

test('admin dapat melihat halaman daftar pengguna beserta filternya', function () {
    $userA = User::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);

    $userB = User::factory()->create([
        'name' => 'Siti Rahma',
        'email' => 'siti@example.com',
        'role_id' => $this->adminRole->id,
        'is_active' => false,
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.index'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Pengguna Sistem');
    $response->assertSee('Budi Santoso');
    $response->assertSee('Siti Rahma');

    // Filter by keyword
    $searchResponse = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.index', ['search' => 'Budi']));
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('Budi Santoso');
    $searchResponse->assertDontSee('Siti Rahma');

    // Filter by role_id
    $roleResponse = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.index', ['role_id' => $this->technicianRole->id]));
    $roleResponse->assertStatus(200);
    $roleResponse->assertSee('Budi Santoso');
    $roleResponse->assertDontSee('Siti Rahma');

    // Filter by is_active
    $activeResponse = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.index', ['is_active' => 0]));
    $activeResponse->assertStatus(200);
    $activeResponse->assertSee('Siti Rahma');
    $activeResponse->assertDontSee('Budi Santoso');
});

test('admin dapat melihat form tambah pengguna', function () {
    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.create'));

    $response->assertStatus(200);
    $response->assertSee('Tambah Pengguna Baru');
    $response->assertSee('Administrator');
    $response->assertSee('Teknisi Lapangan');
});

test('admin berhasil menambahkan pengguna baru dengan data valid', function () {
    $payload = [
        'name' => 'Ahmad Fauzi',
        'email' => 'ahmad.fauzi@monifas.id',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.users.store'), $payload);

    $response->assertRedirect(route('users-and-access.users.index'));
    $response->assertSessionHas('status', 'Pengguna baru berhasil ditambahkan.');

    $user = User::where('email', 'ahmad.fauzi@monifas.id')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Ahmad Fauzi');
    expect($user->role_id)->toBe($this->technicianRole->id);
    expect($user->is_active)->toBeTrue();
    expect(Hash::check('Password123!', $user->password))->toBeTrue();
});

test('validasi pembuatan pengguna gagal jika data tidak lengkap atau salah', function () {
    // Missing required fields
    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.users.store'), [
            'name' => '',
            'email' => 'bukan-email',
            'password' => '123',
            'password_confirmation' => '456',
            'role_id' => 9999,
        ]);

    $response->assertSessionHasErrors(['name', 'email', 'password', 'role_id']);

    // Duplicate email
    $existing = User::factory()->create(['email' => 'duplikat@monifas.id']);

    $dupResponse = $this->actingAs($this->admin)
        ->post(route('users-and-access.users.store'), [
            'name' => 'Pengguna Lain',
            'email' => 'duplikat@monifas.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_id' => $this->adminRole->id,
        ]);

    $dupResponse->assertSessionHasErrors(['email']);
});

test('admin dapat melihat form edit pengguna', function () {
    $targetUser = User::factory()->create([
        'name' => 'Target Edit',
        'email' => 'target@monifas.id',
        'role_id' => $this->technicianRole->id,
    ]);

    $response = $this->withoutVite()
        ->actingAs($this->admin)
        ->get(route('users-and-access.users.edit', $targetUser));

    $response->assertStatus(200);
    $response->assertSee('Edit Profil Pengguna');
    $response->assertSee('Target Edit');
    $response->assertSee('target@monifas.id');
});

test('admin berhasil memperbarui profil pengguna tanpa mengubah password', function () {
    $targetUser = User::factory()->create([
        'name' => 'Nama Lama',
        'email' => 'lama@monifas.id',
        'password' => Hash::make('PasswordLama123!'),
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('users-and-access.users.update', $targetUser), [
            'name' => 'Nama Baru',
            'email' => 'lama@monifas.id',
            'role_id' => $this->adminRole->id,
            'is_active' => false,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response->assertRedirect(route('users-and-access.users.index'));
    $response->assertSessionHas('status', 'Data pengguna berhasil diperbarui.');

    $refreshed = $targetUser->fresh();
    expect($refreshed->name)->toBe('Nama Baru');
    expect($refreshed->role_id)->toBe($this->adminRole->id);
    expect($refreshed->is_active)->toBeFalse();
    expect(Hash::check('PasswordLama123!', $refreshed->password))->toBeTrue();
});

test('admin berhasil memperbarui profil pengguna sekaligus mengubah password', function () {
    $targetUser = User::factory()->create([
        'name' => 'Pengguna Ganti Password',
        'email' => 'ganti@monifas.id',
        'password' => Hash::make('PasswordLama123!'),
        'role_id' => $this->technicianRole->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('users-and-access.users.update', $targetUser), [
            'name' => 'Pengguna Ganti Password',
            'email' => 'ganti@monifas.id',
            'role_id' => $this->technicianRole->id,
            'is_active' => true,
            'password' => 'PasswordBaru456!',
            'password_confirmation' => 'PasswordBaru456!',
        ]);

    $response->assertRedirect(route('users-and-access.users.index'));

    $refreshed = $targetUser->fresh();
    expect(Hash::check('PasswordBaru456!', $refreshed->password))->toBeTrue();
});

test('admin dapat melakukan soft delete pada pengguna', function () {
    $targetUser = User::factory()->create([
        'role_id' => $this->technicianRole->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('users-and-access.users.destroy', $targetUser));

    $response->assertRedirect(route('users-and-access.users.index'));
    $response->assertSessionHas('status', 'Pengguna berhasil dihapus.');

    expect($targetUser->fresh()->trashed())->toBeTrue();
    expect(User::find($targetUser->id))->toBeNull();
    expect(User::withTrashed()->find($targetUser->id))->not->toBeNull();
});

test('admin dapat mengubah status aktif pengguna melalui toggle-active', function () {
    $targetUser = User::factory()->create([
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('users-and-access.users.toggle-active', $targetUser));

    $response->assertRedirect();
    $response->assertSessionHas('status');
    expect($targetUser->fresh()->is_active)->toBeFalse();

    // Toggle kembali menjadi aktif
    $this->actingAs($this->admin)
        ->patch(route('users-and-access.users.toggle-active', $targetUser));
    expect($targetUser->fresh()->is_active)->toBeTrue();
});

test('admin dapat mereset password pengguna secara langsung', function () {
    $targetUser = User::factory()->create([
        'role_id' => $this->technicianRole->id,
        'password' => Hash::make('SebelumReset123!'),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('users-and-access.users.reset-password', $targetUser), [
            'password' => 'ResetSukses789!',
            'password_confirmation' => 'ResetSukses789!',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Kata sandi pengguna berhasil diatur ulang.');

    expect(Hash::check('ResetSukses789!', $targetUser->fresh()->password))->toBeTrue();
});
