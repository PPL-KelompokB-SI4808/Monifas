<?php

namespace App\Services\UsersAndAccess\Users;

use App\DTOs\UsersAndAccess\UserData;
use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Build filtered query for users list based on role, status, and search keywords.
     *
     * @param  array{role_id?: int|null, role?: UserRole|string|null, is_active?: bool|null, search?: string|null}  $filters
     * @return Builder<User>
     */
    public function filterUsersQuery(array $filters = []): Builder
    {
        return User::query()
            ->with('role')
            ->when(isset($filters['role_id']), function (Builder $query) use ($filters) {
                $query->where('role_id', $filters['role_id']);
            })
            ->when(isset($filters['role']), function (Builder $query) use ($filters) {
                $roleSlug = $filters['role'] instanceof UserRole ? $filters['role']->value : (string) $filters['role'];
                $query->whereHas('role', function (Builder $q) use ($roleSlug) {
                    $q->where('slug', $roleSlug);
                });
            })
            ->when(isset($filters['is_active']), function (Builder $query) use ($filters) {
                $query->where('is_active', (bool) $filters['is_active']);
            })
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id');
    }

    /**
     * Get paginated filtered users.
     *
     * @param  array{role_id?: int|null, role?: UserRole|string|null, is_active?: bool|null, search?: string|null}  $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginateUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterUsersQuery($filters)->paginate($perPage);
    }

    /**
     * Create a new user with encrypted password.
     */
    public function createUser(UserData $data): User
    {
        $rawPassword = $data->password ?? Str::password(12);

        $payload = [
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($rawPassword),
            'role_id' => $data->roleId,
            'is_active' => $data->isActive,
        ];

        return User::create($payload);
    }

    /**
     * Update user profile information and rotate role if provided.
     */
    public function updateUser(User $user, UserData $data): User
    {
        $payload = [
            'name' => $data->name,
            'email' => $data->email,
            'role_id' => $data->roleId,
            'is_active' => $data->isActive,
        ];

        if ($data->password !== null && $data->password !== '') {
            $payload['password'] = Hash::make($data->password);
        }

        $user->update($payload);

        return $user->refresh();
    }

    /**
     * Rotate user role explicitly.
     */
    public function rotateRole(User $user, Role|int $role): User
    {
        $roleId = $role instanceof Role ? $role->id : $role;

        $user->update([
            'role_id' => $roleId,
        ]);

        return $user->refresh();
    }

    /**
     * Execute password reset for a given user.
     */
    public function resetPassword(User $user, string $newPassword): bool
    {
        return $user->forceFill([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60),
        ])->save();
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): bool
    {
        $user->is_active = ! $user->is_active;

        return $user->save();
    }

    /**
     * Soft delete a user.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(User $user): bool
    {
        return (bool) $user->restore();
    }
}
