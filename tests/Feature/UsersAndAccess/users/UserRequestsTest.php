<?php

use App\DTOs\UsersAndAccess\UserData;
use App\Http\Requests\UsersAndAccess\Users\ResetPasswordRequest;
use App\Http\Requests\UsersAndAccess\Users\StoreUserRequest;
use App\Http\Requests\UsersAndAccess\Users\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('UserData DTO can be instantiated from array and serialized to array', function () {
    $dto = UserData::fromArray([
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'secret123',
        'role_id' => 2,
        'is_active' => true,
    ]);

    expect($dto->name)->toBe('Budi Santoso');
    expect($dto->email)->toBe('budi@example.com');
    expect($dto->password)->toBe('secret123');
    expect($dto->roleId)->toBe(2);
    expect($dto->isActive)->toBeTrue();

    $array = $dto->toArray();
    expect($array['name'])->toBe('Budi Santoso');
    expect($array['email'])->toBe('budi@example.com');
    expect($array['role_id'])->toBe(2);
    expect($array['is_active'])->toBeTrue();
    expect($array['password'])->toBe('secret123');
});

test('UserData DTO handles optional password gracefully', function () {
    $dto = UserData::fromArray([
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
    ]);

    expect($dto->password)->toBeNull();
    expect($dto->roleId)->toBeNull();
    expect($dto->isActive)->toBeTrue();

    $array = $dto->toArray();
    expect(array_key_exists('password', $array))->toBeFalse();
});

test('StoreUserRequest validates input properly', function () {
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff']);
    $request = new StoreUserRequest;

    // Valid payload
    $validator = Validator::make([
        'name' => 'Ahmad Teknisi',
        'email' => 'ahmad@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $role->id,
        'is_active' => true,
    ], $request->rules());

    expect($validator->passes())->toBeTrue();

    // Invalid payload: duplicate email
    User::factory()->create(['email' => 'ahmad@example.com']);
    $validatorDuplicate = Validator::make([
        'name' => 'Ahmad Teknisi',
        'email' => 'ahmad@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $request->rules());

    expect($validatorDuplicate->fails())->toBeTrue();
    expect($validatorDuplicate->errors()->has('email'))->toBeTrue();

    // Invalid payload: unconfirmed password
    $validatorUnconfirmed = Validator::make([
        'name' => 'Ahmad Teknisi',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ], $request->rules());

    expect($validatorUnconfirmed->fails())->toBeTrue();
    expect($validatorUnconfirmed->errors()->has('password'))->toBeTrue();
});

test('UpdateUserRequest allows existing email for same user but rejects other user email', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $request = new UpdateUserRequest;
    $request->setRouteResolver(fn () => new class($user1)
    {
        public function __construct(private User $user) {}

        public function parameter(string $name, mixed $default = null): mixed
        {
            return $name === 'user' ? $this->user : $default;
        }
    });

    // Valid: keeping own email
    $validatorSame = Validator::make([
        'name' => 'User One Updated',
        'email' => 'user1@example.com',
    ], $request->rules());

    expect($validatorSame->passes())->toBeTrue();

    // Invalid: using user2 email
    $validatorConflict = Validator::make([
        'name' => 'User One Updated',
        'email' => 'user2@example.com',
    ], $request->rules());

    expect($validatorConflict->fails())->toBeTrue();
    expect($validatorConflict->errors()->has('email'))->toBeTrue();
});

test('ResetPasswordRequest requires confirmed password', function () {
    $request = new ResetPasswordRequest;

    $validatorValid = Validator::make([
        'password' => 'new-secret-123',
        'password_confirmation' => 'new-secret-123',
    ], $request->rules());

    expect($validatorValid->passes())->toBeTrue();

    $validatorInvalid = Validator::make([
        'password' => 'new-secret-123',
        'password_confirmation' => 'mismatch',
    ], $request->rules());

    expect($validatorInvalid->fails())->toBeTrue();
    expect($validatorInvalid->errors()->has('password'))->toBeTrue();
});
