<?php

namespace App\Services\LocationAndCategory\DamageCategories;

use App\DTOs\LocationAndCategory\DamageCategoryData;
use App\Models\DamageCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class DamageCategoryService
{
    /**
     * Get paginated list of damage categories with filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, DamageCategory>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return DamageCategory::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $search = trim((string) $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('severity_hint', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['severity_hint']) && $filters['severity_hint'] !== '', function ($query) use ($filters) {
                $query->where('severity_hint', $filters['severity_hint']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new damage category.
     */
    public function store(DamageCategoryData $dto): DamageCategory
    {
        return DamageCategory::create($dto->toArray());
    }

    /**
     * Alias for store().
     */
    public function create(DamageCategoryData $dto): DamageCategory
    {
        return $this->store($dto);
    }

    /**
     * Update an existing damage category.
     */
    public function update(DamageCategory $category, DamageCategoryData $dto): DamageCategory
    {
        $category->update($dto->toArray());

        return $category->fresh() ?? $category;
    }

    /**
     * Check if damage category has connected report dependencies (FR-LCT-06).
     */
    public function hasDependencies(DamageCategory $category): bool
    {
        if (Schema::hasTable('reports')) {
            return DB::table('reports')->where('damage_category_id', $category->id)->exists();
        }

        return false;
    }

    /**
     * Safe deletion of damage category (FR-LCT-06).
     * If it has connected reports, it is marked inactive and soft deleted.
     */
    public function delete(DamageCategory $category): bool
    {
        if ($this->hasDependencies($category)) {
            $category->update(['is_active' => false]);
        }

        return (bool) $category->delete();
    }

    /**
     * Force delete protection (FR-LCT-06).
     * Permanent deletion is forbidden if connected reports exist.
     *
     * @throws RuntimeException
     */
    public function forceDelete(DamageCategory $category): bool
    {
        if ($this->hasDependencies($category)) {
            throw new RuntimeException('Kategori kerusakan yang telah terhubung dengan data laporan dilarang dihapus permanen.');
        }

        return (bool) $category->forceDelete();
    }

    /**
     * Toggle active status of a damage category.
     */
    public function toggleStatus(DamageCategory $category): DamageCategory
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return $category;
    }

    /**
     * Find damage category by ID.
     */
    public function findById(int $id): ?DamageCategory
    {
        return DamageCategory::query()->find($id);
    }

    /**
     * Find damage category by code.
     */
    public function findByCode(string $code): ?DamageCategory
    {
        return DamageCategory::query()
            ->where('code', $code)
            ->first();
    }

    /**
     * Get all active damage categories.
     *
     * @return Collection<int, DamageCategory>
     */
    public function getActiveCategories(): Collection
    {
        return DamageCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get unique severity hints for filtering.
     *
     * @return array<int, string>
     */
    public function getUniqueSeverityHints(): array
    {
        return DamageCategory::query()
            ->whereNotNull('severity_hint')
            ->where('severity_hint', '!=', '')
            ->distinct()
            ->orderBy('severity_hint')
            ->pluck('severity_hint')
            ->toArray();
    }
}
