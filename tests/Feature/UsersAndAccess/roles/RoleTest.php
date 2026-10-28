<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('user role enum contains expected title case keys and string values', function () {
    expect(UserRole::Admin->value)->toBe('admin');
    expect(UserRole::FacilityUser->value)->toBe('facility_user');
    expect(UserRole::Technician->value)->toBe('technician');
    expect(UserRole::Supervisor->value)->toBe('supervisor');
    expect(UserRole::Management->value)->toBe('management');

    expect(UserRole::Admin->label())->toBe('Administrator');
});

test('role can be created and has users relationship', function () {
    $role = Role::create([
        'name' => 'Administrator',
        'slug' => 'admin',
        'description' => 'Akses administrator sistem',
    ]);

    expect($role->id)->not->toBeNull();
    expect($role->name)->toBe('Administrator');
    expect($role->slug)->toBe('admin');

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    expect($role->users)->toHaveCount(1);
    expect($role->users->first()->id)->toBe($user->id);
});

test('role seeder populates exactly 5 default roles', function () {
    $this->seed(RoleSeeder::class);

    expect(Role::count())->toBe(5);

    foreach (UserRole::cases() as $roleEnum) {
        expect(Role::where('slug', $roleEnum->value)->exists())->toBeTrue();
    }
});
