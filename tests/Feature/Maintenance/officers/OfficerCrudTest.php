<?php

use App\Enums\OfficerAvailability;
use App\Enums\UserRole;
use App\Models\MaintenanceOfficer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function createOfficerSupervisor(): User
{
    $role = Role::create([
        'name' => 'Supervisor',
        'slug' => UserRole::Supervisor->value,
    ]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}

test('tamu diarahkan ke login saat membuka halaman daftar officer', function () {
    $response = $this->get(route('maintenance.officers.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-supervisor ditolak saat membuka halaman officer', function () {
    $technicianRole = Role::create([
        'name' => 'Technician',
        'slug' => UserRole::Technician->value,
    ]);
    $technician = User::factory()->create([
        'role_id' => $technicianRole->id,
    ]);

    $response = $this->actingAs($technician)
        ->get(route('maintenance.officers.index'));

    $response->assertForbidden();
});

test('supervisor dapat membuka halaman daftar officer', function () {
    $supervisor = createOfficerSupervisor();

    $response = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.officers.index'));

    $response->assertOk();
    $response->assertSee('Petugas Pemeliharaan');
});

test('supervisor dapat membuka halaman pembuatan officer', function () {
    $supervisor = createOfficerSupervisor();

    $response = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.officers.create'));

    $response->assertOk();
    $response->assertSee('Tambah Petugas');
});

test('supervisor berhasil menyimpan officer', function () {
    $supervisor = createOfficerSupervisor();
    $user = User::factory()->create();

    $response = $this->actingAs($supervisor)
        ->post(route('maintenance.officers.store'), [
            'user_id' => $user->id,
            'employee_code' => 'MNT-CRUD-STORE-01',
            'specialization_notes' => 'Electrical systems',
            'availability_status' => OfficerAvailability::Available->value,
            'max_active_tasks' => 4,
            'is_active' => true,
        ]);

    $response->assertRedirect(route('maintenance.officers.index'));
    $response->assertSessionHas('status', 'Petugas pemeliharaan berhasil ditambahkan.');
    $this->assertDatabaseHas('maintenance_officers', [
        'user_id' => $user->id,
        'employee_code' => 'MNT-CRUD-STORE-01',
        'specialization_notes' => 'Electrical systems',
        'availability_status' => OfficerAvailability::Available->value,
        'max_active_tasks' => 4,
        'is_active' => true,
    ]);
});

test('store menolak data officer yang tidak valid', function () {
    $supervisor = createOfficerSupervisor();

    $response = $this->from(route('maintenance.officers.create'))
        ->actingAs($supervisor)
        ->post(route('maintenance.officers.store'), []);

    $response->assertRedirect(route('maintenance.officers.create'));
    $response->assertSessionHasErrors(['user_id', 'employee_code', 'max_active_tasks']);
    $this->assertDatabaseCount('maintenance_officers', 0);
});

test('supervisor dapat membuka halaman edit officer', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-EDIT-01',
    ]);

    $response = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.officers.edit', $officer));

    $response->assertOk();
    $response->assertSee('Edit Petugas');
});

test('supervisor berhasil memperbarui officer', function () {
    $supervisor = createOfficerSupervisor();
    $user = User::factory()->create();
    $officer = MaintenanceOfficer::create([
        'user_id' => $user->id,
        'employee_code' => 'MNT-CRUD-UPDATE-OLD',
    ]);

    $response = $this->actingAs($supervisor)
        ->put(route('maintenance.officers.update', $officer), [
            'user_id' => $user->id,
            'employee_code' => 'MNT-CRUD-UPDATE-NEW',
            'specialization_notes' => 'Updated specialization',
            'availability_status' => OfficerAvailability::Unavailable->value,
            'max_active_tasks' => 3,
            'is_active' => false,
        ]);

    $response->assertRedirect(route('maintenance.officers.index'));
    $response->assertSessionHas('status', 'Data petugas pemeliharaan berhasil diperbarui.');
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'user_id' => $user->id,
        'employee_code' => 'MNT-CRUD-UPDATE-NEW',
        'specialization_notes' => 'Updated specialization',
        'availability_status' => OfficerAvailability::Unavailable->value,
        'max_active_tasks' => 3,
        'is_active' => false,
    ]);
});

test('update menolak employee code yang sudah digunakan officer lain', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-DUPLICATE-01',
    ]);
    $otherOfficer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-DUPLICATE-02',
    ]);

    $response = $this->from(route('maintenance.officers.edit', $officer))
        ->actingAs($supervisor)
        ->put(route('maintenance.officers.update', $officer), [
            'user_id' => $officer->user_id,
            'employee_code' => $otherOfficer->employee_code,
            'max_active_tasks' => 5,
        ]);

    $response->assertRedirect(route('maintenance.officers.edit', $officer));
    $response->assertSessionHasErrors(['employee_code']);
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'employee_code' => 'MNT-CRUD-DUPLICATE-01',
    ]);
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $otherOfficer->id,
        'employee_code' => 'MNT-CRUD-DUPLICATE-02',
    ]);
});

test('supervisor berhasil menghapus officer secara soft delete', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-DELETE-01',
    ]);

    $response = $this->actingAs($supervisor)
        ->delete(route('maintenance.officers.destroy', $officer));

    $response->assertRedirect(route('maintenance.officers.index'));
    $response->assertSessionHas('status', 'Petugas pemeliharaan berhasil dihapus.');
    $this->assertSoftDeleted('maintenance_officers', [
        'id' => $officer->id,
    ]);
});

test('supervisor dapat mengubah ketersediaan available menjadi unavailable', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-TOGGLE-AVAILABLE',
        'availability_status' => OfficerAvailability::Available,
    ]);

    $response = $this->actingAs($supervisor)
        ->from(route('maintenance.officers.index'))
        ->patch(route('maintenance.officers.toggle-availability', $officer));

    $response->assertRedirect(route('maintenance.officers.index'));
    $response->assertSessionHas('status', 'Status ketersediaan petugas berhasil diubah.');
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'availability_status' => OfficerAvailability::Unavailable->value,
    ]);
});

test('supervisor dapat mengubah ketersediaan unavailable menjadi available', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-TOGGLE-UNAVAILABLE',
        'availability_status' => OfficerAvailability::Unavailable,
    ]);

    $response = $this->actingAs($supervisor)
        ->from(route('maintenance.officers.index'))
        ->patch(route('maintenance.officers.toggle-availability', $officer));

    $response->assertRedirect(route('maintenance.officers.index'));
    $response->assertSessionHas('status', 'Status ketersediaan petugas berhasil diubah.');
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'availability_status' => OfficerAvailability::Available->value,
    ]);
});

test('supervisor tidak mengubah status officer yang sedang busy saat toggle', function () {
    $supervisor = createOfficerSupervisor();
    $officer = MaintenanceOfficer::create([
        'user_id' => User::factory()->create()->id,
        'employee_code' => 'MNT-CRUD-TOGGLE-BUSY',
        'availability_status' => OfficerAvailability::Busy,
    ]);

    $response = $this->actingAs($supervisor)
        ->from(route('maintenance.officers.index'))
        ->patch(route('maintenance.officers.toggle-availability', $officer));

    $response->assertRedirect(route('maintenance.officers.index'));
    $this->assertDatabaseHas('maintenance_officers', [
        'id' => $officer->id,
        'availability_status' => OfficerAvailability::Busy->value,
    ]);
});
