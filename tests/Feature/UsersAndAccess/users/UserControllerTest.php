<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('unauthenticated user cannot access user management', function () {
    $response = $this->get('/dashboard/users-and-access/users');

    $response->assertRedirect('/login');
});

test('non-admin user is forbidden from accessing user management with 403', function () {
    $techRole = Role::create(['name' => 'Teknisi', 'slug' => UserRole::Technician->value]);
    $user = User::factory()->create(['role_id' => $techRole->id, 'is_active' => true]);

    $response = $this->actingAs($user)->get('/dashboard/users-and-access/users');

    $response->assertStatus(403);
});

test('inactive admin user is forbidden from accessing user management', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $user = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => false]);

    $response = $this->actingAs($user)->get('/dashboard/users-and-access/users');

    $response->assertStatus(403);
});

test('admin user can view user index and create page', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    $response = $this->withoutVite()->actingAs($admin)->get('/dashboard/users-and-access/users');
    $response->assertStatus(200);

    $responseCreate = $this->withoutVite()->actingAs($admin)->get('/dashboard/users-and-access/users/create');
    $responseCreate->assertStatus(200);
});

test('admin can store new user via UserController', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    $response = $this->actingAs($admin)->post('/dashboard/users-and-access/users', [
        'name' => 'Petugas Baru',
        'email' => 'petugas@monifas.id',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('users-and-access.users.index'));
    $response->assertSessionHas('status');

    $created = User::where('email', 'petugas@monifas.id')->first();
    expect($created)->not->toBeNull();
    expect($created->name)->toBe('Petugas Baru');
    expect(Hash::check('password123', $created->password))->toBeTrue();
});

test('admin can update user and toggle active status', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    $targetUser = User::factory()->create([
        'name' => 'User Lama',
        'email' => 'lama@monifas.id',
        'is_active' => true,
    ]);

    // Update
    $responseUpdate = $this->actingAs($admin)->put("/dashboard/users-and-access/users/{$targetUser->id}", [
        'name' => 'User Baru',
        'email' => 'lama@monifas.id',
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);

    $responseUpdate->assertRedirect(route('users-and-access.users.index'));
    expect($targetUser->fresh()->name)->toBe('User Baru');

    // Toggle Active
    $responseToggle = $this->actingAs($admin)->patch("/dashboard/users-and-access/users/{$targetUser->id}/toggle-active");
    $responseToggle->assertStatus(302);
    expect($targetUser->fresh()->is_active)->toBeFalse();
});

test('admin can reset user password and soft delete user', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);
    $targetUser = User::factory()->create();

    // Reset password
    $responseReset = $this->actingAs($admin)->post("/dashboard/users-and-access/users/{$targetUser->id}/reset-password", [
        'password' => 'new-secret-pass123',
        'password_confirmation' => 'new-secret-pass123',
    ]);

    $responseReset->assertStatus(302);
    expect(Hash::check('new-secret-pass123', $targetUser->fresh()->password))->toBeTrue();

    // Destroy (soft delete)
    $responseDelete = $this->actingAs($admin)->delete("/dashboard/users-and-access/users/{$targetUser->id}");
    $responseDelete->assertRedirect(route('users-and-access.users.index'));
    expect($targetUser->fresh()->trashed())->toBeTrue();
});
