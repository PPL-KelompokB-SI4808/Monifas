<?php

use App\DTOs\UsersAndAccess\UserData;
use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use App\Services\UsersAndAccess\Users\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('UserService filterUsersQuery filters by role, active status, and search query', function () {
    $service = new UserService;

    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $technicianRole = Role::create(['name' => 'Technician', 'slug' => UserRole::Technician->value]);

    $adminUser = User::factory()->create([
        'name' => 'Admin Alpha',
        'email' => 'admin@monifas.id',
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);

    $technicianUser = User::factory()->create([
        'name' => 'Teknisi Beta',
        'email' => 'beta@monifas.id',
        'role_id' => $technicianRole->id,
        'is_active' => false,
    ]);

    // Filter by role_id
    $resultRole = $service->filterUsersQuery(['role_id' => $adminRole->id])->get();
    expect($resultRole)->toHaveCount(1);
    expect($resultRole->first()->id)->toBe($adminUser->id);

    // Filter by role enum
    $resultEnum = $service->filterUsersQuery(['role' => UserRole::Technician])->get();
    expect($resultEnum)->toHaveCount(1);
    expect($resultEnum->first()->id)->toBe($technicianUser->id);

    // Filter by active status
    $resultActive = $service->filterUsersQuery(['is_active' => false])->get();
    expect($resultActive)->toHaveCount(1);
    expect($resultActive->first()->id)->toBe($technicianUser->id);

    // Search by name
    $resultSearch = $service->filterUsersQuery(['search' => 'Beta'])->get();
    expect($resultSearch)->toHaveCount(1);
    expect($resultSearch->first()->id)->toBe($technicianUser->id);

    // Paginate
    $paginated = $service->paginateUsers([], 10);
    expect($paginated->total())->toBe(2);
});

test('UserService createUser creates user with encrypted password and proper attributes', function () {
    $service = new UserService;
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff']);

    $dto = new UserData(
        name: 'Citra Lestari',
        email: 'citra@example.com',
        password: 'rahasia-banget',
        roleId: $role->id,
        isActive: true
    );

    $user = $service->createUser($dto);

    expect($user->id)->not->toBeNull();
    expect($user->name)->toBe('Citra Lestari');
    expect($user->email)->toBe('citra@example.com');
    expect($user->role_id)->toBe($role->id);
    expect($user->is_active)->toBeTrue();
    expect(Hash::check('rahasia-banget', $user->password))->toBeTrue();
});

test('UserService updateUser updates profile and rotates role', function () {
    $service = new UserService;
    $roleA = Role::create(['name' => 'Role A', 'slug' => 'role_a']);
    $roleB = Role::create(['name' => 'Role B', 'slug' => 'role_b']);

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role_id' => $roleA->id,
        'is_active' => true,
    ]);

    $updateDto = new UserData(
        name: 'New Name',
        email: 'new@example.com',
        password: 'new-password-123',
        roleId: $roleB->id,
        isActive: false
    );

    $updated = $service->updateUser($user, $updateDto);

    expect($updated->name)->toBe('New Name');
    expect($updated->email)->toBe('new@example.com');
    expect($updated->role_id)->toBe($roleB->id);
    expect($updated->is_active)->toBeFalse();
    expect(Hash::check('new-password-123', $updated->password))->toBeTrue();
});

test('UserService rotateRole changes user role directly', function () {
    $service = new UserService;
    $roleA = Role::create(['name' => 'Role A', 'slug' => 'role_a']);
    $roleB = Role::create(['name' => 'Role B', 'slug' => 'role_b']);

    $user = User::factory()->create(['role_id' => $roleA->id]);

    $service->rotateRole($user, $roleB);
    expect($user->role_id)->toBe($roleB->id);

    $service->rotateRole($user, $roleA->id);
    expect($user->role_id)->toBe($roleA->id);
});

test('UserService resetPassword updates password and regenerates remember token', function () {
    $service = new UserService;
    $user = User::factory()->create(['remember_token' => 'old-token']);

    $success = $service->resetPassword($user, 'super-reset-pass');

    expect($success)->toBeTrue();
    expect(Hash::check('super-reset-pass', $user->fresh()->password))->toBeTrue();
    expect($user->fresh()->remember_token)->not->toBe('old-token');
});

test('UserService toggleActive switches active state', function () {
    $service = new UserService;
    $user = User::factory()->create(['is_active' => true]);

    $service->toggleActive($user);
    expect($user->fresh()->is_active)->toBeFalse();

    $service->toggleActive($user);
    expect($user->fresh()->is_active)->toBeTrue();
});

test('UserService deleteUser and restoreUser handle soft deletes', function () {
    $service = new UserService;
    $user = User::factory()->create();

    $service->deleteUser($user);
    expect($user->fresh()->trashed())->toBeTrue();
    expect(User::find($user->id))->toBeNull();

    $service->restoreUser($user);
    expect($user->fresh()->trashed())->toBeFalse();
    expect(User::find($user->id))->not->toBeNull();
});

test('UserService createUser generates random password when password is not provided', function () {
    $service = new UserService;
    $role = Role::create(['name' => 'Auto Pass Role', 'slug' => 'auto_pass_role']);

    $dto = new UserData(
        name: 'Auto Pass User',
        email: 'autopass@example.com',
        password: null,
        roleId: $role->id,
        isActive: true
    );

    $user = $service->createUser($dto);

    expect($user->id)->not->toBeNull();
    expect($user->password)->not->toBeNull();
    expect(strlen($user->password))->toBeGreaterThan(20);
});

test('UserService updateUser preserves existing password when password is null or empty', function () {
    $service = new UserService;
    $role = Role::create(['name' => 'Preserve Pass Role', 'slug' => 'preserve_pass_role']);

    $user = User::factory()->create([
        'password' => Hash::make('original-password'),
        'role_id' => $role->id,
    ]);

    $updateDto = new UserData(
        name: 'Updated Name Only',
        email: $user->email,
        password: null,
        roleId: $role->id,
        isActive: true
    );

    $service->updateUser($user, $updateDto);

    expect(Hash::check('original-password', $user->fresh()->password))->toBeTrue();
});
