<?php

namespace App\Services\LocationAndCategory\Locations;

use App\DTOs\LocationAndCategory\LocationData;
use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * Get paginated list of locations with filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Location>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Location::query()
            ->with(['parent'])
            ->withCount(['children', 'buildings', 'facilities'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $search = trim((string) $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['region']) && $filters['region'] !== '', function ($query) use ($filters) {
                $query->where('region', $filters['region']);
            })
            ->when(isset($filters['parent_id']) && $filters['parent_id'] !== '', function ($query) use ($filters) {
                if ($filters['parent_id'] === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', (int) $filters['parent_id']);
                }
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new location.
     */
    public function store(LocationData $dto): Location
    {
        return Location::create($dto->toArray());
    }

    /**
     * Alias for store().
     */
    public function create(LocationData $dto): Location
    {
        return $this->store($dto);
    }

    /**
     * Update an existing location.
     */
    public function update(Location $location, LocationData $dto): Location
    {
        $location->update($dto->toArray());

        return $location->fresh(['parent', 'children']) ?? $location;
    }

    /**
     * Safe deletion of location:
     * If it has child locations, buildings, or facilities, deactivate it instead of hard/soft deleting.
     */
    public function delete(Location $location): bool
    {
        if ($location->children()->exists() || $location->buildings()->exists() || $location->facilities()->exists()) {
            return (bool) $location->update(['is_active' => false]);
        }

        return (bool) $location->delete();
    }

    /**
     * Toggle active status of a location.
     */
    public function toggleStatus(Location $location): Location
    {
        $location->update([
            'is_active' => ! $location->is_active,
        ]);

        return $location;
    }

    /**
     * Find location by ID with relations.
     */
    public function findById(int $id): ?Location
    {
        return Location::query()
            ->with(['parent', 'children', 'buildings', 'facilities'])
            ->withCount(['children', 'buildings', 'facilities'])
            ->find($id);
    }

    /**
     * Get all active locations.
     *
     * @return Collection<int, Location>
     */
    public function getActiveLocations(): Collection
    {
        return Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all unique regions for filtering.
     *
     * @return array<int, string>
     */
    public function getUniqueRegions(): array
    {
        return Location::query()
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region')
            ->toArray();
    }

    /**
     * Get hierarchy tree of root locations with their children.
     *
     * @return Collection<int, Location>
     */
    public function getTree(bool $onlyActive = false): Collection
    {
        return Location::query()
            ->whereNull('parent_id')
            ->when($onlyActive, fn ($q) => $q->where('is_active', true))
            ->with(['children' => function ($q) use ($onlyActive) {
                $q->when($onlyActive, fn ($sub) => $sub->where('is_active', true))
                    ->with(['children' => function ($sub2) use ($onlyActive) {
                        $sub2->when($onlyActive, fn ($s) => $s->where('is_active', true));
                    }]);
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get candidate parent locations excluding self and its descendants to avoid circular hierarchy.
     *
     * @return Collection<int, Location>
     */
    public function getParentCandidates(?Location $currentLocation = null, bool $onlyActive = true): Collection
    {
        $query = Location::query()
            ->when($onlyActive, fn ($q) => $q->where('is_active', true))
            ->orderBy('name');

        if ($currentLocation && $currentLocation->exists) {
            $excludedIds = array_merge([$currentLocation->id], $this->getDescendantIds($currentLocation));
            $query->whereNotIn('id', $excludedIds);
        }

        return $query->get();
    }

    /**
     * Recursively retrieve all descendant IDs of a location.
     *
     * @return array<int, int>
     */
    public function getDescendantIds(Location $location): array
    {
        $descendantIds = [];
        $children = Location::where('parent_id', $location->id)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $descendantIds[] = (int) $childId;
            $childModel = Location::find($childId);
            if ($childModel) {
                $descendantIds = array_merge($descendantIds, $this->getDescendantIds($childModel));
            }
        }

        return array_unique($descendantIds);
    }
}
