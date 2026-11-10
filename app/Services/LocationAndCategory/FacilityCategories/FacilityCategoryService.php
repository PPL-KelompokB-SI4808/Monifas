<?php

namespace App\Services\LocationAndCategory\FacilityCategories;

use App\DTOs\LocationAndCategory\FacilityCategoryData;
use App\Models\FacilityCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class FacilityCategoryService
{
    /**
     * Get paginated list of facility categories with filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, FacilityCategory>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return FacilityCategory::query()
            ->withCount('facilities')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $search = trim((string) $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new facility category.
     */
    public function store(FacilityCategoryData $dto): FacilityCategory
    {
        return FacilityCategory::create($dto->toArray());
    }

    /**
     * Alias for store().
     */
    public function create(FacilityCategoryData $dto): FacilityCategory
    {
        return $this->store($dto);
    }

    /**
     * Update an existing facility category.
     */
    public function update(FacilityCategory $category, FacilityCategoryData $dto): FacilityCategory
    {
        $category->update($dto->toArray());

        return $category->fresh() ?? $category;
    }

    /**
     * Check if facility category has connected dependencies (FR-LCT-06).
     */
    public function hasDependencies(FacilityCategory $category): bool
    {
        return $category->facilities()->exists();
    }

    /**
     * Safe deletion of facility category (FR-LCT-06).
     * If it has connected facilities, it is marked inactive and soft deleted.
     */
    public function delete(FacilityCategory $category): bool
    {
        if ($this->hasDependencies($category)) {
            $category->update(['is_active' => false]);
        }

        return (bool) $category->delete();
    }

    /**
     * Force delete protection (FR-LCT-06).
     * Permanent deletion is forbidden if connected facilities exist.
     *
     * @throws RuntimeException
     */
    public function forceDelete(FacilityCategory $category): bool
    {
        if ($this->hasDependencies($category)) {
            throw new RuntimeException('Kategori fasilitas yang telah terhubung dengan data fasilitas dilarang dihapus permanen.');
        }

        return (bool) $category->forceDelete();
    }

    /**
     * Toggle active status of a facility category.
     */
    public function toggleStatus(FacilityCategory $category): FacilityCategory
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return $category;
    }

    /**
     * Find facility category by ID.
     */
    public function findById(int $id): ?FacilityCategory
    {
        return FacilityCategory::query()
            ->withCount('facilities')
            ->find($id);
    }

    /**
     * Find facility category by code.
     */
    public function findByCode(string $code): ?FacilityCategory
    {
        return FacilityCategory::query()
            ->where('code', $code)
            ->first();
    }

    /**
     * Get all active facility categories.
     *
     * @return Collection<int, FacilityCategory>
     */
    public function getActiveCategories(): Collection
    {
        return FacilityCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
