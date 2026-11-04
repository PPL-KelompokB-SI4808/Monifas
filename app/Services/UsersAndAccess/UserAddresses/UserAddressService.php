<?php

namespace App\Services\UsersAndAccess\UserAddresses;

use App\DTOs\UsersAndAccess\UserAddressData;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserAddressService
{
    /**
     * Build filtered query for user addresses.
     *
     * @param  array{user_id?: int|null, is_primary?: bool|null, search?: string|null}  $filters
     * @return Builder<UserAddress>
     */
    public function filterUserAddressesQuery(array $filters = []): Builder
    {
        return UserAddress::query()
            ->with('user')
            ->when(isset($filters['user_id']), function (Builder $query) use ($filters) {
                $query->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['is_primary']), function (Builder $query) use ($filters) {
                $query->where('is_primary', (bool) $filters['is_primary']);
            })
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);
                $query->where(function (Builder $q) use ($search) {
                    $q->where('label', 'like', "%{$search}%")
                        ->orWhere('building_or_unit', 'like', "%{$search}%")
                        ->orWhere('address_detail', 'like', "%{$search}%")
                        ->orWhere('contact_phone', 'like', "%{$search}%")
                        ->orWhere('postal_code', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('is_primary')
            ->latest('id');
    }

    /**
     * Get paginated filtered user addresses.
     *
     * @param  array{user_id?: int|null, is_primary?: bool|null, search?: string|null}  $filters
     * @return LengthAwarePaginator<int, UserAddress>
     */
    public function paginateUserAddresses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterUserAddressesQuery($filters)->paginate($perPage);
    }

    /**
     * Create a new user address with atomic primary flag switching.
     */
    public function createAddress(UserAddressData $data): UserAddress
    {
        return DB::transaction(function () use ($data) {
            if ($data->isPrimary) {
                UserAddress::where('user_id', $data->userId)
                    ->update(['is_primary' => false]);
            }

            return UserAddress::create($data->toArray());
        });
    }

    /**
     * Update an existing user address with atomic primary flag switching.
     */
    public function updateAddress(UserAddress $address, UserAddressData $data): UserAddress
    {
        return DB::transaction(function () use ($address, $data) {
            if ($data->isPrimary) {
                UserAddress::where('user_id', $data->userId)
                    ->where('id', '!=', $address->id)
                    ->update(['is_primary' => false]);
            }

            $address->update($data->toArray());

            return $address->refresh();
        });
    }

    /**
     * Delete an existing user address.
     */
    public function deleteAddress(UserAddress $address): bool
    {
        return (bool) $address->delete();
    }

    /**
     * Set a specific address as primary for its owner.
     */
    public function setAsPrimary(UserAddress $address): UserAddress
    {
        return DB::transaction(function () use ($address) {
            UserAddress::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);

            $address->update(['is_primary' => true]);

            return $address->refresh();
        });
    }
}
