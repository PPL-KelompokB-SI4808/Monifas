<?php

namespace App\Http\Controllers\UsersAndAccess\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersAndAccess\Roles\StoreRoleRequest;
use App\Http\Requests\UsersAndAccess\Roles\UpdateRoleRequest;
use App\Models\Role;
use App\Services\UsersAndAccess\Roles\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use InvalidArgumentException;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin'),
        ];
    }

    public function __construct(
        protected RoleService $roleService,
    ) {}

    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = $this->roleService->paginateRoles(15);

        return view('pages.dashboard.users-and-access.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        return view('pages.dashboard.users-and-access.roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->validated());

        return redirect()
            ->route('users-and-access.roles.index')
            ->with('status', 'Peran baru berhasil ditambahkan.');
    }

    /**
     * Display the specified role and its authorization scopes.
     */
    public function show(Role $role): View
    {
        $role->load('users');
        $scopes = $this->roleService->getAuthorizationScopes($role);

        return view('pages.dashboard.users-and-access.roles.show', compact('role', 'scopes'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        return view('pages.dashboard.users-and-access.roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->updateRole($role, $request->validated());

        return redirect()
            ->route('users-and-access.roles.index')
            ->with('status', 'Data peran berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->deleteRole($role);

            return redirect()
                ->route('users-and-access.roles.index')
                ->with('status', 'Peran berhasil dihapus.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('users-and-access.roles.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
