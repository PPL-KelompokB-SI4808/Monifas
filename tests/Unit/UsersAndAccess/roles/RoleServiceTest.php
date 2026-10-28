<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use App\Services\UsersAndAccess\Roles\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('RoleService performs full CRUD on roles', function () {
    $service = new RoleService;

    // Create
    $role = $service->createRole([
        'name' => 'Koordinator Ruangan',
        'slug' => 'koordinator-ruangan',
        'description' => 'Penanggung jawab ruangan',
    ]);

    expect($role->id)->not->toBeNull();
    expect($role->slug)->toBe('koordinator-ruangan');

    // Find
    expect($service->findRoleById($role->id)?->id)->toBe($role->id);
    expect($service->findRoleBySlug('koordinator-ruangan')?->id)->toBe($role->id);

    // Update
    $updated = $service->updateRole($role, [
        'name' => 'Koordinator Ruang Baru',
        'slug' => 'koordinator-ruang-baru',
    ]);

    expect($updated->name)->toBe('Koordinator Ruang Baru');
    expect($updated->slug)->toBe('koordinator-ruang-baru');

    // Delete
    $deleted = $service->deleteRole($updated);
    expect($deleted)->toBeTrue();
    expect(Role::find($role->id))->toBeNull();
});

test('RoleService deleteRole throws exception when role has assigned users', function () {
    $service = new RoleService;
    $role = Role::create(['name' => 'Supervisor', 'slug' => 'supervisor']);

    User::factory()->create(['role_id' => $role->id]);

    expect(fn () => $service->deleteRole($role))->toThrow(InvalidArgumentException::class);

    // Force delete succeeds
    $forced = $service->deleteRole($role, force: true);
    expect($forced)->toBeTrue();
});

test('RoleService assignRoleToUser assigns role by Model, ID, and slug', function () {
    $service = new RoleService;
    $roleA = Role::create(['name' => 'Role A', 'slug' => 'role_a']);
    $roleB = Role::create(['name' => 'Role B', 'slug' => 'role_b']);

    $user = User::factory()->create();

    $service->assignRoleToUser($user, $roleA);
    expect($user->role_id)->toBe($roleA->id);

    $service->assignRoleToUser($user, $roleB->id);
    expect($user->role_id)->toBe($roleB->id);

    $service->assignRoleToUser($user, 'role_a');
    expect($user->role_id)->toBe($roleA->id);
});

test('RoleService authorization scopes return expected scopes and evaluate hasScope correctly', function () {
    $service = new RoleService;

    // Admin has wildcard '*'
    $adminScopes = $service->getAuthorizationScopes(UserRole::Admin);
    expect($adminScopes)->toContain('*');
    expect($service->hasScope(UserRole::Admin, 'anything.can.be.done'))->toBeTrue();

    // Technician scopes
    $techScopes = $service->getAuthorizationScopes(UserRole::Technician);
    expect($techScopes)->toContain('repair.repairs');
    expect($service->hasScope(UserRole::Technician, 'repair.repairs'))->toBeTrue();
    expect($service->hasScope(UserRole::Technician, 'finance.audit'))->toBeFalse();

    // FacilityUser scopes
    $userScopes = $service->getAuthorizationScopes(UserRole::FacilityUser);
    expect($userScopes)->toContain('reporting.reports.create');
    expect($service->hasScope(UserRole::FacilityUser, 'reporting.reports.create'))->toBeTrue();
    expect($service->hasScope(UserRole::FacilityUser, 'repair.repairs'))->toBeFalse();

    // Supervisor scopes
    expect($service->hasScope(UserRole::Supervisor, 'maintenance.assignments'))->toBeTrue();

    // Management scopes
    expect($service->hasScope(UserRole::Management, 'monitoring-and-analytic.dashboard'))->toBeTrue();
});
