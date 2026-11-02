<?php

namespace App\Http\Controllers\UsersAndAccess\UserAddresses;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersAndAccess\UserAddresses\StoreUserAddressRequest;
use App\Http\Requests\UsersAndAccess\UserAddresses\UpdateUserAddressRequest;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\UsersAndAccess\UserAddresses\UserAddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class UserAddressController extends Controller implements HasMiddleware
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
        protected UserAddressService $addressService,
    ) {}

    /**
     * Display a listing of user addresses.
     */
    public function index(Request $request): View
    {
        $filters = [
            'user_id' => $request->filled('user_id') ? (int) $request->input('user_id') : null,
            'is_primary' => $request->filled('is_primary') ? $request->boolean('is_primary') : null,
            'search' => $request->input('search'),
        ];

        $addresses = $this->addressService->paginateUserAddresses($filters, 15);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('pages.dashboard.users-and-access.user-addresses.index', compact('addresses', 'users', 'filters'));
    }

    /**
     * Show the form for creating a new user address.
     */
    public function create(Request $request): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $selectedUserId = $request->filled('user_id') ? (int) $request->input('user_id') : null;

        return view('pages.dashboard.users-and-access.user-addresses.create', compact('users', 'selectedUserId'));
    }

    /**
     * Store a newly created user address in storage.
     */
    public function store(StoreUserAddressRequest $request): RedirectResponse
    {
        $this->addressService->createAddress($request->toDto());

        return redirect()
            ->route('users-and-access.user-addresses.index')
            ->with('status', 'Alamat pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user address.
     */
    public function edit(UserAddress $userAddress): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('pages.dashboard.users-and-access.user-addresses.edit', compact('userAddress', 'users'));
    }

    /**
     * Update the specified user address in storage.
     */
    public function update(UpdateUserAddressRequest $request, UserAddress $userAddress): RedirectResponse
    {
        $this->addressService->updateAddress($userAddress, $request->toDto());

        return redirect()
            ->route('users-and-access.user-addresses.index')
            ->with('status', 'Alamat pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user address from storage.
     */
    public function destroy(UserAddress $userAddress): RedirectResponse
    {
        $this->addressService->deleteAddress($userAddress);

        return redirect()
            ->route('users-and-access.user-addresses.index')
            ->with('status', 'Alamat pengguna berhasil dihapus.');
    }
}
