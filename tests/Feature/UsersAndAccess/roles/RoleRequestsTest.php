<?php

use App\Http\Requests\UsersAndAccess\Roles\StoreRoleRequest;
use App\Http\Requests\UsersAndAccess\Roles\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('StoreRoleRequest validates valid and invalid payloads', function () {
    $request = new StoreRoleRequest;

    // Valid
    $validatorValid = Validator::make([
        'name' => 'Koordinator Lapangan',
        'slug' => 'koordinator_lapangan',
        'description' => 'Koordinator divisi lapangan',
    ], $request->rules());

    expect($validatorValid->passes())->toBeTrue();

    // Duplicate slug
    Role::create(['name' => 'Koordinator', 'slug' => 'koordinator']);
    $validatorDuplicate = Validator::make([
        'name' => 'Koordinator Baru',
        'slug' => 'koordinator',
    ], $request->rules());

    expect($validatorDuplicate->fails())->toBeTrue();
    expect($validatorDuplicate->errors()->has('slug'))->toBeTrue();

    // Invalid slug format (with space)
    $validatorInvalidSlug = Validator::make([
        'name' => 'Nama Acak',
        'slug' => 'nama acak',
    ], $request->rules());

    expect($validatorInvalidSlug->fails())->toBeTrue();
    expect($validatorInvalidSlug->errors()->has('slug'))->toBeTrue();
});

test('UpdateRoleRequest allows same slug for current role but prevents duplicate across roles', function () {
    $role1 = Role::create(['name' => 'Staff A', 'slug' => 'staff_a']);
    $role2 = Role::create(['name' => 'Staff B', 'slug' => 'staff_b']);

    $request = new UpdateRoleRequest;
    $request->setRouteResolver(fn () => new class($role1)
    {
        public function __construct(private Role $role) {}

        public function parameter(string $name, mixed $default = null): mixed
        {
            return $name === 'role' ? $this->role : $default;
        }
    });

    // Valid: keeping same slug
    $validatorSame = Validator::make([
        'name' => 'Staff A Updated',
        'slug' => 'staff_a',
    ], $request->rules());

    expect($validatorSame->passes())->toBeTrue();

    // Invalid: conflict with role2 slug
    $validatorConflict = Validator::make([
        'name' => 'Staff A Updated',
        'slug' => 'staff_b',
    ], $request->rules());

    expect($validatorConflict->fails())->toBeTrue();
    expect($validatorConflict->errors()->has('slug'))->toBeTrue();
});
