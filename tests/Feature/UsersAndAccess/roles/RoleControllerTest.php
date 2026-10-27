<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('unauthenticated user cannot access role management', function () {
    $response = $this->get('/dashboard/users-and-access/roles');

    $response->assertRedirect('/login');
});

test('non-admin user is forbidden from role management with 403', function () {
    $role = Role::create(['name' => 'Supervisor', 'slug' => UserRole::Supervisor->value]);
    $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true]);

    $response = $this->actingAs($user)->get('/dashboard/users-and-access/roles');

    $response->assertStatus(403);
});

test('admin can view roles index and create page', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    $response = $this->withoutVite()->actingAs($admin)->get('/dashboard/users-and-access/roles');
    $response->assertStatus(200);

    $responseCreate = $this->withoutVite()->actingAs($admin)->get('/dashboard/users-and-access/roles/create');
    $responseCreate->assertStatus(200);
});

test('admin can store, update, and delete role via RoleController', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    // Store
    $responseStore = $this->actingAs($admin)->post('/dashboard/users-and-access/roles', [
        'name' => 'Manajer Aset',
        'slug' => 'manajer_aset',
        'description' => 'Penanggung jawab inventaris',
    ]);

    $responseStore->assertRedirect(route('users-and-access.roles.index'));
    $role = Role::where('slug', 'manajer_aset')->first();
    expect($role)->not->toBeNull();

    // Update
    $responseUpdate = $this->actingAs($admin)->put("/dashboard/users-and-access/roles/{$role->id}", [
        'name' => 'Manajer Aset & Logistik',
        'slug' => 'manajer_aset',
        'description' => 'Penanggung jawab inventaris dan logistik',
    ]);

    $responseUpdate->assertRedirect(route('users-and-access.roles.index'));
    expect($role->fresh()->name)->toBe('Manajer Aset & Logistik');

    // Destroy
    $responseDestroy = $this->actingAs($admin)->delete("/dashboard/users-and-access/roles/{$role->id}");
    $responseDestroy->assertRedirect(route('users-and-access.roles.index'));
    expect(Role::find($role->id))->toBeNull();
});

test('RoleController prevents deleting a role with active users', function () {
    $adminRole = Role::create(['name' => 'Admin', 'slug' => UserRole::Admin->value]);
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

    $targetRole = Role::create(['name' => 'Operator', 'slug' => 'operator']);
    User::factory()->create(['role_id' => $targetRole->id]);

    $response = $this->actingAs($admin)->delete("/dashboard/users-and-access/roles/{$targetRole->id}");

    $response->assertRedirect(route('users-and-access.roles.index'));
    $response->assertSessionHasErrors(['error']);
    expect(Role::find($targetRole->id))->not->toBeNull();
});
