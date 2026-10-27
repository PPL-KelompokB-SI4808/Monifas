<?php

namespace App\Services\UsersAndAccess\Roles;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RoleService
{
    /**
     * Get all roles ordered with user count.
     *
     * @return Collection<int, Role>
     */
    public function getAllRoles(): Collection
    {
        return Role::withCount('users')->orderBy('id')->get();
    }

    /**
     * Get paginated roles list.
     *
     * @return LengthAwarePaginator<int, Role>
     */
    public function paginateRoles(int $perPage = 15): LengthAwarePaginator
    {
        return Role::withCount('users')->latest('id')->paginate($perPage);
    }

    /**
     * Find role by ID.
     */
    public function findRoleById(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Find role by slug.
     */
    public function findRoleBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    /**
     * Create a new role.
     *
     * @param  array{name: string, slug?: string|null, description?: string|null}  $data
     */
    public function createRole(array $data): Role
    {
        $slug = ! empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);

        return Role::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update an existing role.
     *
     * @param  array{name?: string, slug?: string|null, description?: string|null}  $data
     */
    public function updateRole(Role $role, array $data): Role
    {
        $payload = [];

        if (isset($data['name'])) {
            $payload['name'] = $data['name'];
        }

        if (isset($data['slug'])) {
            $payload['slug'] = $data['slug'];
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = $data['description'];
        }

        $role->update($payload);

        return $role->refresh();
    }

    /**
     * Delete role with safety check against attached users.
     *
     * @throws InvalidArgumentException
     */
    public function deleteRole(Role $role, bool $force = false): bool
    {
        if (! $force && $role->users()->count() > 0) {
            throw new InvalidArgumentException("Tidak dapat menghapus peran '{$role->name}' karena masih memiliki pengguna terkait.");
        }

        return (bool) $role->delete();
    }

    /**
     * Assign a role to a user.
     */
    public function assignRoleToUser(User $user, Role|int|string $role): User
    {
        $roleModel = match (true) {
            $role instanceof Role => $role,
            is_int($role) => Role::findOrFail($role),
            is_string($role) => Role::where('slug', $role)->firstOrFail(),
        };

        $user->update([
            'role_id' => $roleModel->id,
        ]);

        return $user->refresh();
    }

    /**
     * Get authorized module scopes defined for a role.
     *
     * @return array<int, string>
     */
    public function getAuthorizationScopes(Role|UserRole|string $role): array
    {
        $slug = match (true) {
            $role instanceof Role => $role->slug,
            $role instanceof UserRole => $role->value,
            default => (string) $role,
        };

        return match ($slug) {
            UserRole::Admin->value => [
                '*',
            ],
            UserRole::FacilityUser->value => [
                'reporting.reports.create',
                'reporting.reports.view_own',
                'supporting.feedback',
                'supporting.announcements',
            ],
            UserRole::Technician->value => [
                'repair.repairs',
                'repair.materials',
                'maintenance.schedules',
                'reporting.reports.view_assigned',
            ],
            UserRole::Supervisor->value => [
                'maintenance.officers',
                'maintenance.assignments',
                'maintenance.schedules',
                'monitoring-and-analytic.sla-monitoring',
                'reporting.reports.manage',
            ],
            UserRole::Management->value => [
                'monitoring-and-analytic.dashboard',
                'monitoring-and-analytic.maintenance-report',
                'monitoring-and-analytic.sla-monitoring',
                'supporting.campaigns',
            ],
            default => [],
        };
    }

    /**
     * Check if a given role has access to a specific scope/permission.
     */
    public function hasScope(Role|UserRole|string $role, string $scope): bool
    {
        $scopes = $this->getAuthorizationScopes($role);

        if (in_array('*', $scopes, true)) {
            return true;
        }

        return in_array($scope, $scopes, true);
    }
}
