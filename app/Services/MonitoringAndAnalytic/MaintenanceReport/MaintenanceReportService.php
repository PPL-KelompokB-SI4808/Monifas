<?php

namespace App\Services\MonitoringAndAnalytic\MaintenanceReport;

use App\Enums\RepairExecutionStatus;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Location;
use App\Models\MaintenanceOfficer;
use App\Models\Repair;
use App\Models\RepairMaterial;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaintenanceReportService
{
    /**
     * Aggregate all maintenance report data for the index view.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getReportData(array $filters = [], int $perPage = 15): array
    {
        return [
            'history' => $this->getMaintenanceHistory($filters, $perPage),
            'summary' => $this->getSummaryMetrics($filters),
            'damage_frequency' => $this->getDamageFrequency($filters),
            'cost_analysis' => $this->getCostAndMaterialAnalysis($filters),
            'preventive_vs_reactive' => $this->getPreventiveVsReactive($filters),
        ];
    }

    /**
     * Retrieve paginated maintenance & repair history.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Repair>
     */
    public function getMaintenanceHistory(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyRepairFilters(Repair::query(), $filters)
            ->with([
                'report.damageCategory',
                'facility.location',
                'facility.building',
                'facility.category',
                'officer.user:id,name',
                'assignment',
                'materials',
            ])
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Aggregate high-level summary metrics for filtered repairs.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     total_repairs: int,
     *     completed_repairs: int,
     *     in_progress_repairs: int,
     *     total_cost: float,
     *     avg_duration_hours: float
     * }
     */
    public function getSummaryMetrics(array $filters = []): array
    {
        $repairs = $this->applyRepairFilters(Repair::query(), $filters)
            ->with('materials')
            ->get(['id', 'status', 'started_at', 'ended_at']);

        $totalRepairs = $repairs->count();
        $completedRepairs = $repairs->where('status', RepairExecutionStatus::Completed)->count();
        $inProgressRepairs = $repairs->where('status', RepairExecutionStatus::InProgress)->count();

        $totalCost = (float) $repairs->sum(
            fn (Repair $repair): float => (float) $repair->materials->sum('subtotal')
        );

        $durations = $repairs
            ->filter(fn (Repair $repair): bool => $repair->ended_at !== null)
            ->map(function (Repair $repair): float {
                /** @var Carbon $endedAt */
                $endedAt = $repair->ended_at;

                return max(0.0, $repair->started_at->diffInMinutes($endedAt) / 60);
            });

        $avgDurationHours = $durations->isNotEmpty() ? round((float) $durations->avg(), 1) : 0.0;

        return [
            'total_repairs' => $totalRepairs,
            'completed_repairs' => $completedRepairs,
            'in_progress_repairs' => $inProgressRepairs,
            'total_cost' => round($totalCost, 2),
            'avg_duration_hours' => $avgDurationHours,
        ];
    }

    /**
     * Analyze damage/repair frequency per facility and per facility category.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     by_facility: array<int, array{
     *         facility_id: int,
     *         facility_name: string,
     *         building_name: string,
     *         category_name: string,
     *         repair_count: int,
     *         total_cost: float
     *     }>,
     *     by_category: array<int, array{
     *         category_id: int,
     *         category_name: string,
     *         repair_count: int,
     *         percentage: float
     *     }>
     * }
     */
    public function getDamageFrequency(array $filters = []): array
    {
        $repairs = $this->applyRepairFilters(Repair::query(), $filters)
            ->with(['facility.building', 'facility.category', 'materials'])
            ->get(['id', 'facility_id']);

        $total = $repairs->count();

        $byFacilityMap = [];
        $byCategoryMap = [];

        foreach ($repairs as $repair) {
            $facilityId = (int) $repair->facility_id;
            $facility = $repair->facility;
            $repairCost = (float) $repair->materials->sum('subtotal');

            if (! isset($byFacilityMap[$facilityId])) {
                $byFacilityMap[$facilityId] = [
                    'facility_id' => $facilityId,
                    'facility_name' => $facility !== null ? $facility->name : 'Fasilitas #'.$facilityId,
                    'building_name' => $facility !== null && $facility->building !== null ? $facility->building->name : '-',
                    'category_name' => $facility !== null && $facility->category !== null ? $facility->category->name : '-',
                    'repair_count' => 0,
                    'total_cost' => 0.0,
                ];
            }

            $byFacilityMap[$facilityId]['repair_count']++;
            $byFacilityMap[$facilityId]['total_cost'] = round($byFacilityMap[$facilityId]['total_cost'] + $repairCost, 2);

            $categoryId = $facility !== null ? (int) $facility->facility_category_id : 0;
            $categoryName = $facility !== null && $facility->category !== null ? $facility->category->name : 'Tanpa Kategori';

            if (! isset($byCategoryMap[$categoryId])) {
                $byCategoryMap[$categoryId] = [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'repair_count' => 0,
                    'percentage' => 0.0,
                ];
            }

            $byCategoryMap[$categoryId]['repair_count']++;
        }

        $byFacility = array_values($byFacilityMap);
        usort($byFacility, fn (array $a, array $b): int => $b['repair_count'] <=> $a['repair_count']);
        $byFacility = array_slice($byFacility, 0, 10);

        $byCategory = array_values($byCategoryMap);
        foreach ($byCategory as &$categoryItem) {
            $categoryItem['percentage'] = $total > 0 ? round(($categoryItem['repair_count'] / $total) * 100, 1) : 0.0;
        }
        unset($categoryItem);
        usort($byCategory, fn (array $a, array $b): int => $b['repair_count'] <=> $a['repair_count']);

        return [
            'by_facility' => $byFacility,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Analyze repair material costs and consumption breakdown.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     total_cost: float,
     *     total_material_items: int,
     *     total_quantity: float,
     *     by_material_type: array<int, array{
     *         material_type: string,
     *         item_count: int,
     *         total_quantity: float,
     *         total_cost: float
     *     }>,
     *     top_materials: array<int, array{
     *         material_name: string,
     *         material_type: string,
     *         unit: string,
     *         total_quantity: float,
     *         total_cost: float
     *     }>
     * }
     */
    public function getCostAndMaterialAnalysis(array $filters = []): array
    {
        $repairIds = $this->applyRepairFilters(Repair::query(), $filters)->pluck('id');

        if ($repairIds->isEmpty()) {
            return [
                'total_cost' => 0.0,
                'total_material_items' => 0,
                'total_quantity' => 0.0,
                'by_material_type' => [],
                'top_materials' => [],
            ];
        }

        $materials = RepairMaterial::query()
            ->whereIn('repair_id', $repairIds)
            ->get(['material_name', 'material_type', 'unit', 'quantity', 'subtotal']);

        $byType = $materials
            ->groupBy('material_type')
            ->map(fn (Collection $group, string $type): array => [
                'material_type' => $type !== '' ? $type : 'Lainnya',
                'item_count' => $group->count(),
                'total_quantity' => round((float) $group->sum('quantity'), 2),
                'total_cost' => round((float) $group->sum('subtotal'), 2),
            ])
            ->sortByDesc('total_cost')
            ->values()
            ->all();

        $topMaterials = $materials
            ->groupBy(fn (RepairMaterial $m): string => $m->material_name.'|'.$m->unit)
            ->map(function (Collection $group): array {
                $first = $group->first();

                return [
                    'material_name' => $first !== null ? $first->material_name : '-',
                    'material_type' => $first !== null ? $first->material_type : '-',
                    'unit' => $first !== null ? $first->unit : '-',
                    'total_quantity' => round((float) $group->sum('quantity'), 2),
                    'total_cost' => round((float) $group->sum('subtotal'), 2),
                ];
            })
            ->sortByDesc('total_cost')
            ->take(10)
            ->values()
            ->all();

        return [
            'total_cost' => round((float) $materials->sum('subtotal'), 2),
            'total_material_items' => $materials->count(),
            'total_quantity' => round((float) $materials->sum('quantity'), 2),
            'by_material_type' => $byType,
            'top_materials' => $topMaterials,
        ];
    }

    /**
     * Compare preventive maintenance vs reactive/emergency repairs.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     total: int,
     *     preventive: array{count: int, percentage: float, total_cost: float},
     *     reactive: array{count: int, percentage: float, total_cost: float}
     * }
     */
    public function getPreventiveVsReactive(array $filters = []): array
    {
        $repairs = $this->applyRepairFilters(Repair::query(), $filters)
            ->with(['assignment:id,maintenance_schedule_id', 'materials:id,repair_id,subtotal'])
            ->get(['id', 'assignment_id', 'report_id']);

        $preventiveCount = 0;
        $reactiveCount = 0;
        $preventiveCost = 0.0;
        $reactiveCost = 0.0;

        foreach ($repairs as $repair) {
            $cost = (float) $repair->materials->sum('subtotal');
            $isPreventive = $repair->report_id === null
                || ($repair->assignment !== null && $repair->assignment->maintenance_schedule_id !== null);

            if ($isPreventive) {
                $preventiveCount++;
                $preventiveCost += $cost;
            } else {
                $reactiveCount++;
                $reactiveCost += $cost;
            }
        }

        $total = $preventiveCount + $reactiveCount;

        return [
            'total' => $total,
            'preventive' => [
                'count' => $preventiveCount,
                'percentage' => $total > 0 ? round(($preventiveCount / $total) * 100, 1) : 0.0,
                'total_cost' => round($preventiveCost, 2),
            ],
            'reactive' => [
                'count' => $reactiveCount,
                'percentage' => $total > 0 ? round(($reactiveCount / $total) * 100, 1) : 0.0,
                'total_cost' => round($reactiveCost, 2),
            ],
        ];
    }

    /**
     * Stream a UTF-8 BOM CSV export (Excel-compatible) of filtered maintenance & repair history.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(array $filters = []): StreamedResponse
    {
        $repairs = $this->applyRepairFilters(Repair::query(), $filters)
            ->with([
                'report.damageCategory',
                'facility.location',
                'facility.building',
                'officer.user:id,name',
                'assignment:id,maintenance_schedule_id',
                'materials',
            ])
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->get();

        $filename = 'maintenance-report-'.Carbon::now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($repairs): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM for Microsoft Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID Perbaikan',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Fasilitas',
                'Lokasi',
                'Gedung',
                'Jenis Pemeliharaan',
                'Kategori Kerusakan',
                'Teknisi',
                'Status',
                'Total Biaya Material (IDR)',
                'Material Digunakan',
                'Tindakan Teknis',
            ]);

            foreach ($repairs as $repair) {
                $isPreventive = $repair->report_id === null
                    || ($repair->assignment !== null && $repair->assignment->maintenance_schedule_id !== null);

                $totalCost = (float) $repair->materials->sum('subtotal');
                $materialSummary = $repair->materials
                    ->map(fn (RepairMaterial $m): string => "{$m->material_name} ({$m->quantity} {$m->unit})")
                    ->implode('; ');

                fputcsv($handle, [
                    'REP-'.$repair->id,
                    $repair->started_at->format('Y-m-d H:i'),
                    $repair->ended_at !== null ? $repair->ended_at->format('Y-m-d H:i') : '-',
                    $repair->facility !== null ? $repair->facility->name : '-',
                    $repair->facility !== null && $repair->facility->location !== null ? $repair->facility->location->name : '-',
                    $repair->facility !== null && $repair->facility->building !== null ? $repair->facility->building->name : '-',
                    $isPreventive ? 'Preventive Maintenance' : 'Reaktif / Darurat',
                    $repair->report !== null && $repair->report->damageCategory !== null ? $repair->report->damageCategory->name : '-',
                    $repair->officer !== null && $repair->officer->user !== null ? $repair->officer->user->name : '-',
                    $repair->status->value,
                    number_format($totalCost, 2, '.', ''),
                    $materialSummary !== '' ? $materialSummary : '-',
                    $repair->technical_action,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Gather unpaginated data for print / PDF export view.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getPrintableData(array $filters = []): array
    {
        $records = $this->applyRepairFilters(Repair::query(), $filters)
            ->with([
                'report.damageCategory',
                'facility.location',
                'facility.building',
                'facility.category',
                'officer.user:id,name',
                'assignment',
                'materials',
            ])
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->get();

        return [
            'records' => $records,
            'summary' => $this->getSummaryMetrics($filters),
            'damage_frequency' => $this->getDamageFrequency($filters),
            'cost_analysis' => $this->getCostAndMaterialAnalysis($filters),
            'preventive_vs_reactive' => $this->getPreventiveVsReactive($filters),
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Retrieve dropdown options for maintenance report filters.
     *
     * @return array{
     *     locations: Collection<int, Location>,
     *     buildings: Collection<int, Building>,
     *     facilities: Collection<int, Facility>,
     *     facility_categories: Collection<int, FacilityCategory>,
     *     damage_categories: Collection<int, DamageCategory>,
     *     officers: Collection<int, MaintenanceOfficer>,
     *     statuses: array<int, RepairExecutionStatus>
     * }
     */
    public function getFilterOptions(): array
    {
        return [
            'locations' => Location::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'buildings' => Building::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'location_id']),
            'facilities' => Facility::query()->orderBy('name')->get(['id', 'name', 'building_id']),
            'facility_categories' => FacilityCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'damage_categories' => DamageCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'officers' => MaintenanceOfficer::query()->with('user:id,name')->where('is_active', true)->get(['id', 'user_id', 'employee_code']),
            'statuses' => RepairExecutionStatus::cases(),
        ];
    }

    /**
     * Apply maintenance report filters to a Repair query.
     *
     * @param  Builder<Repair>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Repair>
     */
    protected function applyRepairFilters(Builder $query, array $filters): Builder
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($filters);

        return $query
            ->when($startDate !== null, fn (Builder $q) => $q->where('started_at', '>=', $startDate))
            ->when($endDate !== null, fn (Builder $q) => $q->where('started_at', '<=', $endDate))
            ->when(filled($filters['facility_id'] ?? null), fn (Builder $q) => $q->where('facility_id', $filters['facility_id']))
            ->when(filled($filters['officer_id'] ?? null), fn (Builder $q) => $q->where('officer_id', $filters['officer_id']))
            ->when(filled($filters['status'] ?? null), fn (Builder $q) => $q->where('status', $filters['status']))
            ->when(
                filled($filters['location_id'] ?? null)
                || filled($filters['building_id'] ?? null)
                || filled($filters['facility_category_id'] ?? null),
                function (Builder $q) use ($filters): void {
                    $q->whereHas('facility', function (Builder $fq) use ($filters): void {
                        $fq
                            ->when(filled($filters['location_id'] ?? null), fn (Builder $sub) => $sub->where('location_id', $filters['location_id']))
                            ->when(filled($filters['building_id'] ?? null), fn (Builder $sub) => $sub->where('building_id', $filters['building_id']))
                            ->when(filled($filters['facility_category_id'] ?? null), fn (Builder $sub) => $sub->where('facility_category_id', $filters['facility_category_id']));
                    });
                }
            )
            ->when(filled($filters['damage_category_id'] ?? null), function (Builder $q) use ($filters): void {
                $q->whereHas('report', fn (Builder $rq) => $rq->where('damage_category_id', $filters['damage_category_id']));
            })
            ->when(filled($filters['search'] ?? null), function (Builder $q) use ($filters): void {
                $term = '%'.trim((string) $filters['search']).'%';
                $q->where(function (Builder $sub) use ($term): void {
                    $sub->where('technical_action', 'like', $term)
                        ->orWhere('repair_result', 'like', $term)
                        ->orWhereHas('facility', fn (Builder $fq) => $fq->where('name', 'like', $term));
                });
            });
    }

    /**
     * Resolve start and end Carbon dates from explicit dates or named period presets.
     *
     * @param  array<string, mixed>  $filters
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    protected function resolvePeriodRange(array $filters): array
    {
        if (filled($filters['start_date'] ?? null) || filled($filters['end_date'] ?? null)) {
            $start = filled($filters['start_date'] ?? null)
                ? Carbon::parse((string) $filters['start_date'])->startOfDay()
                : null;
            $end = filled($filters['end_date'] ?? null)
                ? Carbon::parse((string) $filters['end_date'])->endOfDay()
                : null;

            return [$start, $end];
        }

        $period = $filters['period'] ?? null;
        if (! is_string($period) || $period === '' || $period === 'custom') {
            return [null, null];
        }

        $now = Carbon::now();

        return match ($period) {
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'quarter' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [null, null],
        };
    }
}
