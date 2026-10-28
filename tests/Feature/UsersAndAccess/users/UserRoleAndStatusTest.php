<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('user belongs to a role and hasRole helper works correctly', function () {
    $adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
    ]);

    $technicianRole = Role::create([
        'name' => 'Teknisi',
        'slug' => UserRole::Technician->value,
    ]);

    $adminUser = User::factory()->create([
        'role_id' => $adminRole->id,
    ]);

    expect($adminUser->role)->not->toBeNull();
    expect($adminUser->role->id)->toBe($adminRole->id);
    expect($adminUser->hasRole(UserRole::Admin))->toBeTrue();
    expect($adminUser->hasRole(UserRole::Technician))->toBeFalse();
    expect($adminUser->hasRole(UserRole::Supervisor))->toBeFalse();
});

test('user has soft deletes enabled', function () {
    $user = User::factory()->create();

    $user->delete();

    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id))->not->toBeNull();
    expect($user->trashed())->toBeTrue();
});

test('user has userAddresses and notifications relationships defined', function () {
    $user = User::factory()->create();

    expect($user->userAddresses())->toBeInstanceOf(HasMany::class);
    expect($user->notifications())->toBeInstanceOf(HasMany::class);
});

test('user seeder populates initial users for all 5 roles', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(UserSeeder::class);

    $admin = User::where('email', 'admin@monifas.id')->first();
    $facilityUser = User::where('email', 'user@monifas.id')->first();
    $technician = User::where('email', 'technician@monifas.id')->first();
    $supervisor = User::where('email', 'supervisor@monifas.id')->first();
    $management = User::where('email', 'management@monifas.id')->first();

    expect($admin)->not->toBeNull();
    expect($admin->hasRole(UserRole::Admin))->toBeTrue();
    expect($admin->is_active)->toBeTrue();

    expect($facilityUser)->not->toBeNull();
    expect($facilityUser->hasRole(UserRole::FacilityUser))->toBeTrue();

    expect($technician)->not->toBeNull();
    expect($technician->hasRole(UserRole::Technician))->toBeTrue();

    expect($supervisor)->not->toBeNull();
    expect($supervisor->hasRole(UserRole::Supervisor))->toBeTrue();

    expect($management)->not->toBeNull();
    expect($management->hasRole(UserRole::Management))->toBeTrue();
});
