<?php

namespace App\Http\Controllers\UsersAndAccess\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersAndAccess\Users\ResetPasswordRequest;
use App\Http\Requests\UsersAndAccess\Users\StoreUserRequest;
use App\Http\Requests\UsersAndAccess\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\UsersAndAccess\Roles\RoleService;
use App\Services\UsersAndAccess\Users\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class UserController extends Controller implements HasMiddleware
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
        protected UserService $userService,
        protected RoleService $roleService,
    ) {}

    /**
     * Display a listing of users with filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'role_id' => $request->filled('role_id') ? (int) $request->input('role_id') : null,
            'is_active' => $request->filled('is_active') ? $request->boolean('is_active') : null,
            'search' => $request->input('search'),
        ];

        $users = $this->userService->paginateUsers($filters, 15);
        $roles = $this->roleService->getAllRoles();

        return view('pages.dashboard.users-and-access.users.index', compact('users', 'roles', 'filters'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = $this->roleService->getAllRoles();

        return view('pages.dashboard.users-and-access.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->toDto());

        return redirect()
            ->route('users-and-access.users.index')
            ->with('status', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): View
    {
        $user->load(['role', 'userAddresses']);

        return view('pages.dashboard.users-and-access.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = $this->roleService->getAllRoles();

        return view('pages.dashboard.users-and-access.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->toDto());

        return redirect()
            ->route('users-and-access.users.index')
            ->with('status', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->deleteUser($user);

        return redirect()
            ->route('users-and-access.users.index')
            ->with('status', 'Pengguna berhasil dihapus.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        $this->userService->toggleActive($user);

        return back()->with('status', 'Status aktif pengguna berhasil diubah.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $this->userService->resetPassword($user, (string) $request->validated('password'));

        return back()->with('status', 'Kata sandi pengguna berhasil diatur ulang.');
    }
}
