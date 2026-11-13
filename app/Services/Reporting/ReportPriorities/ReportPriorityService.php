<?php

namespace App\Services\Reporting\ReportPriorities;

use App\DTOs\Reporting\ReportPriorityData;
use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ReportPriorityService
{
    /**
     * Standard urgency sequence: Emergency (1) -> High (2) -> Medium (3) -> Low (4).
     */
    public const URGENCY_SORT_MAP = [
        'emergency' => 1,
        'high' => 2,
        'medium' => 3,
        'low' => 4,
    ];

    /**
     * Get paginated list of report priorities with optional filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, ReportPriority>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return ReportPriority::query()
            ->orderedByUrgency()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $search = trim((string) $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('level', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['level']) && $filters['level'] !== '', function ($query) use ($filters) {
                $query->where('level', $filters['level']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all active report priorities ordered by urgency.
     *
     * @return Collection<int, ReportPriority>
     */
    public function getAllActiveOrdered(): Collection
    {
        return ReportPriority::query()
            ->where('is_active', true)
            ->orderedByUrgency()
            ->get();
    }

    /**
     * Validate SLA boundary rules.
     *
     * Rule: SLA Resolution hours must be greater than or equal to SLA Response hours.
     *
     * @throws InvalidArgumentException
     */
    public function validateSlaHours(int $responseHours, int $resolutionHours): void
    {
        if ($responseHours < 1) {
            throw new InvalidArgumentException('Target jam respon awal (SLA Response) minimal 1 jam.');
        }

        if ($resolutionHours < 1) {
            throw new InvalidArgumentException('Target jam penyelesaian (SLA Resolution) minimal 1 jam.');
        }

        if ($resolutionHours < $responseHours) {
            throw new InvalidArgumentException('Target jam penyelesaian (SLA Resolution) tidak boleh lebih cepat daripada target jam respon.');
        }
    }

    /**
     * Automatically resolve sort order based on urgency level:
     * Emergency (1) -> High (2) -> Medium (3) -> Low (4).
     */
    public function resolveSortOrder(PriorityLevel|string $level, ?int $explicitSortOrder = null): int
    {
        if ($explicitSortOrder !== null && $explicitSortOrder > 0) {
            return $explicitSortOrder;
        }

        $levelKey = $level instanceof PriorityLevel ? $level->value : strtolower(trim((string) $level));

        return self::URGENCY_SORT_MAP[$levelKey] ?? (ReportPriority::max('sort_order') + 1);
    }

    /**
     * Store a newly created report priority.
     */
    public function store(ReportPriorityData $dto): ReportPriority
    {
        $this->validateSlaHours($dto->slaResponseHours, $dto->slaResolutionHours);

        $sortOrder = $this->resolveSortOrder($dto->level, $dto->sortOrder);

        $payload = $dto->toArray();
        $payload['sort_order'] = $sortOrder;

        return ReportPriority::create($payload);
    }

    /**
     * Update an existing report priority.
     */
    public function update(ReportPriority $priority, ReportPriorityData $dto): ReportPriority
    {
        $this->validateSlaHours($dto->slaResponseHours, $dto->slaResolutionHours);

        $sortOrder = $this->resolveSortOrder($dto->level, $dto->sortOrder ?? $priority->sort_order);

        $payload = $dto->toArray();
        $payload['sort_order'] = $sortOrder;

        $priority->update($payload);

        return $priority->fresh() ?? $priority;
    }

    /**
     * Delete or deactivate a report priority safely.
     */
    public function delete(ReportPriority $priority): bool
    {
        return (bool) $priority->delete();
    }

    /**
     * Toggle active status of a report priority.
     */
    public function toggleStatus(ReportPriority $priority): ReportPriority
    {
        $priority->update([
            'is_active' => ! $priority->is_active,
        ]);

        return $priority;
    }

    /**
     * Synchronize and automatically reorder all priorities according to standard urgency:
     * Emergency (1) -> High (2) -> Medium (3) -> Low (4).
     */
    public function syncStandardUrgencyOrder(): void
    {
        $priorities = ReportPriority::all();

        foreach ($priorities as $priority) {
            $expectedOrder = self::URGENCY_SORT_MAP[$priority->level->value] ?? $priority->sort_order;

            if ($priority->sort_order !== $expectedOrder) {
                $priority->update(['sort_order' => $expectedOrder]);
            }
        }
    }
}
