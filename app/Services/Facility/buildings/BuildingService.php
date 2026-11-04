<?php

namespace App\Services\Facility\Buildings;

use App\DTOs\Facility\BuildingData;
use App\Models\Building;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BuildingService
{
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Building::query()
            ->with(['location', 'areaZone'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($filters['location_id'] ?? null, function ($query, $locationId) {
                $query->where('location_id', $locationId); // Penyaringan berdasarkan lokasi kawasan
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']); // Penyaringan status keaktifan
            })
            ->latest()
            ->paginate($perPage);
    }

    public function store(BuildingData $dto): Building
    {
        return Building::create($dto->toArray());
    }

    public function update(Building $building, BuildingData $dto): Building
    {
        $building->update($dto->toArray());

        return $building;
    }

    public function delete(Building $building): bool
    {
        // Mekanisme penghapusan aman (Soft Delete / Safe Deactivation)
        // Jika gedung memiliki keterkaitan dengan ruangan, fasilitas, atau laporan aktif,
        // maka diset dinonaktifkan (is_active = false) agar integritas referensial terjaga.
        if ($building->rooms()->exists() || $building->facilities()->exists() || $building->reports()->exists()) {
            return $building->update(['is_active' => false]);
        }

        return $building->delete();
    }
}
