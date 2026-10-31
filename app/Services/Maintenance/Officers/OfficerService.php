<?php

namespace App\Services\Maintenance\Officers;

use App\Enums\OfficerAvailability;
use App\Models\MaintenanceOfficer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use UnexpectedValueException;

class OfficerService
{
    /**
     * Get all maintenance officers.
     *
     * @return Collection<int, MaintenanceOfficer>
     */
    public function getAllOfficers(): Collection
    {
        return MaintenanceOfficer::with('user')->latest('id')->get();
    }

    /**
     * Get paginated maintenance officers.
     *
     * @return LengthAwarePaginator<int, MaintenanceOfficer>
     */
    public function paginateOfficers(int $perPage = 15): LengthAwarePaginator
    {
        return MaintenanceOfficer::with('user')->latest('id')->paginate($perPage);
    }

    /**
     * Create a maintenance officer.
     *
     * @param  array<string, mixed>  $data
     */
    public function createOfficer(array $data): MaintenanceOfficer
    {
        return MaintenanceOfficer::create($data);
    }

    /**
     * Update a maintenance officer.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateOfficer(MaintenanceOfficer $officer, array $data): MaintenanceOfficer
    {
        $officer->update($data);

        return $officer->refresh();
    }

    /**
     * Soft delete a maintenance officer.
     */
    public function deleteOfficer(MaintenanceOfficer $officer): bool
    {
        return (bool) $officer->delete();
    }

    /**
     * Toggle between available and unavailable; busy officers remain busy.
     */
    public function toggleAvailability(MaintenanceOfficer $officer): bool
    {
        $availabilityStatus = $officer->getAttribute('availability_status');

        if (is_string($availabilityStatus)) {
            $availabilityStatus = OfficerAvailability::from($availabilityStatus);
        }

        if (! $availabilityStatus instanceof OfficerAvailability) {
            throw new UnexpectedValueException('Status ketersediaan petugas tidak valid.');
        }

        $officer->availability_status = match ($availabilityStatus) {
            OfficerAvailability::Available => OfficerAvailability::Unavailable->value,
            OfficerAvailability::Unavailable => OfficerAvailability::Available->value,
            OfficerAvailability::Busy => OfficerAvailability::Busy->value,
        };

        return $officer->save();
    }

    /**
     * Synchronize an officer's skills.
     *
     * @param  array<int, int>  $skillIds
     */
    public function syncSkills(MaintenanceOfficer $officer, array $skillIds): void
    {
        // TODO: Implement when the facility_categories table is available.
    }

    /**
     * Synchronize an officer's zones.
     *
     * @param  array<int, int>  $zoneIds
     */
    public function syncZones(MaintenanceOfficer $officer, array $zoneIds): void
    {
        // TODO: Implement when the locations and area_zones tables are available.
    }

    /**
     * Get an officer's history.
     */
    public function getHistory(MaintenanceOfficer $officer): void
    {
        // TODO: Implement when the assignments table is available.
    }
}
