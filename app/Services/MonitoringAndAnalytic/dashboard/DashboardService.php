<?php

namespace App\Services\MonitoringAndAnalytic\Dashboard;

use App\Enums\AssignmentStatus;
use App\Enums\FacilityCondition;
use App\Enums\ReportStatus;
use App\Models\Assignment;
use App\Models\Facility;
use App\Models\Report;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Aggregate all dashboard analytics data for the given filters.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     kpi: array{
     *         active_reports: int,
     *         completed_reports: int,
     *         problematic_assets: int,
     *         maintenance_tasks: int
     *     },
     *     trends: array{
     *         labels: array<int, string>,
     *         datasets: array{
     *             incoming: array<int, int>,
     *             completed: array<int, int>
     *         }
     *     },
     *     technician_performance: array{
     *         summary: array{
     *             total_tasks: int,
     *             completed_tasks: int,
     *             completion_rate: float,
     *             avg_resolution_hours: float
     *         },
     *         technicians: array<int, array{
     *             officer_id: int,
     *             officer_name: string,
     *             employee_code: string,
     *             total_tasks: int,
     *             completed_tasks: int,
     *             active_tasks: int,
     *             completion_rate: float,
     *             avg_resolution_hours: float
     *         }>
     *     }
     * }
     */
    public function getDashboardData(array $filters = []): array
    {
        return [
            'kpi' => $this->getKpiSummary($filters),
            'trends' => $this->getReportTrends($filters),
            'technician_performance' => $this->getTechnicianPerformance($filters),
        ];
    }

    /**
     * Aggregate KPI summary cards: active reports, completed reports, problematic assets, and active maintenance tasks.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     active_reports: int,
     *     completed_reports: int,
     *     problematic_assets: int,
     *     maintenance_tasks: int
     * }
     */
    public function getKpiSummary(array $filters = []): array
    {
        $reportQuery = $this->applyReportFilters(Report::query(), $filters);

        $activeReports = (clone $reportQuery)
            ->whereNotIn('status', [ReportStatus::Completed, ReportStatus::Rejected])
            ->count();

        $completedReports = (clone $reportQuery)
            ->where('status', ReportStatus::Completed)
            ->count();

        $problematicAssets = Facility::query()
            ->where('condition', '!=', FacilityCondition::Good)
            ->when(filled($filters['location_id'] ?? null), fn (Builder $q) => $q->where('location_id', $filters['location_id']))
            ->when(filled($filters['building_id'] ?? null), fn (Builder $q) => $q->where('building_id', $filters['building_id']))
            ->when(filled($filters['facility_id'] ?? null), fn (Builder $q) => $q->where('id', $filters['facility_id']))
            ->count();

        $maintenanceTasks = $this->applyAssignmentFilters(Assignment::query(), $filters)
            ->whereIn('status', [
                AssignmentStatus::Assigned,
                AssignmentStatus::Accepted,
                AssignmentStatus::InProgress,
            ])
            ->count();

        return [
            'active_reports' => $activeReports,
            'completed_reports' => $completedReports,
            'problematic_assets' => $problematicAssets,
            'maintenance_tasks' => $maintenanceTasks,
        ];
    }

    /**
     * Calculate periodic report volume trends (incoming vs completed) formatted for Chart.js.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     labels: array<int, string>,
     *     datasets: array{
     *         incoming: array<int, int>,
     *         completed: array<int, int>
     *     }
     * }
     */
    public function getReportTrends(array $filters = [], int $months = 6): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($filters, $months);
        $useDailyBuckets = $startDate->diffInDays($endDate) <= 31;

        $bucketFormat = $useDailyBuckets ? 'Y-m-d' : 'Y-m';
        $labelFormat = $useDailyBuckets ? 'd M' : 'M Y';
        $step = $useDailyBuckets ? '1 day' : '1 month';

        $periodStart = $useDailyBuckets ? $startDate->copy()->startOfDay() : $startDate->copy()->startOfMonth();
        $periodEnd = $useDailyBuckets ? $endDate->copy()->startOfDay() : $endDate->copy()->startOfMonth();

        $buckets = [];
        foreach (CarbonPeriod::create($periodStart, $step, $periodEnd) as $date) {
            if ($date === null) {
                continue;
            }
            $key = $date->format($bucketFormat);
            $buckets[$key] = [
                'label' => $date->translatedFormat($labelFormat),
                'incoming' => 0,
                'completed' => 0,
            ];
        }

        $reports = $this->applyReportFilters(Report::query(), array_merge($filters, [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]))
            ->get(['id', 'status', 'created_at', 'completed_at']);

        foreach ($reports as $report) {
            if ($report->created_at !== null) {
                $createdKey = $report->created_at->format($bucketFormat);
                if (isset($buckets[$createdKey])) {
                    $buckets[$createdKey]['incoming']++;
                }
            }

            if ($report->status === ReportStatus::Completed && $report->completed_at !== null) {
                $completedKey = $report->completed_at->format($bucketFormat);
                if (isset($buckets[$completedKey])) {
                    $buckets[$completedKey]['completed']++;
                }
            }
        }

        $labels = [];
        $incoming = [];
        $completed = [];

        foreach ($buckets as $bucket) {
            $labels[] = $bucket['label'];
            $incoming[] = $bucket['incoming'];
            $completed[] = $bucket['completed'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'incoming' => $incoming,
                'completed' => $completed,
            ],
        ];
    }

    /**
     * Calculate technician team task completion performance metrics.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     summary: array{
     *         total_tasks: int,
     *         completed_tasks: int,
     *         completion_rate: float,
     *         avg_resolution_hours: float
     *     },
     *     technicians: array<int, array{
     *         officer_id: int,
     *         officer_name: string,
     *         employee_code: string,
     *         total_tasks: int,
     *         completed_tasks: int,
     *         active_tasks: int,
     *         completion_rate: float,
     *         avg_resolution_hours: float
     *     }>
     * }
     */
    public function getTechnicianPerformance(array $filters = []): array
    {
        $assignments = $this->applyAssignmentFilters(Assignment::query(), $filters)
            ->with(['officer.user:id,name'])
            ->whereNotIn('status', [AssignmentStatus::Cancelled, AssignmentStatus::Reassigned])
            ->get([
                'id',
                'officer_id',
                'status',
                'started_at',
                'completed_at',
                'created_at',
            ]);

        $activeStatuses = [
            AssignmentStatus::Assigned,
            AssignmentStatus::Accepted,
            AssignmentStatus::InProgress,
        ];

        $technicians = $assignments
            ->groupBy('officer_id')
            ->map(function (Collection $officerAssignments, int|string $officerId) use ($activeStatuses): array {
                $first = $officerAssignments->first();
                $officer = $first?->officer;
                $totalTasks = $officerAssignments->count();

                $completedAssignments = $officerAssignments->where('status', AssignmentStatus::Completed);
                $completedTasks = $completedAssignments->count();
                $activeTasks = $officerAssignments->whereIn('status', $activeStatuses)->count();

                $avgHours = $this->calculateAverageResolutionHours($completedAssignments);

                $officerName = $officer !== null && $officer->user !== null
                    ? $officer->user->name
                    : 'Teknisi #'.$officerId;
                $employeeCode = $officer !== null ? $officer->employee_code : '-';

                return [
                    'officer_id' => (int) $officerId,
                    'officer_name' => $officerName,
                    'employee_code' => $employeeCode,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'active_tasks' => $activeTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0.0,
                    'avg_resolution_hours' => $avgHours,
                ];
            })
            ->sortByDesc('completed_tasks')
            ->values()
            ->all();

        $totalTasks = $assignments->count();
        $completedAssignments = $assignments->where('status', AssignmentStatus::Completed);
        $completedTasks = $completedAssignments->count();

        return [
            'summary' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0.0,
                'avg_resolution_hours' => $this->calculateAverageResolutionHours($completedAssignments),
            ],
            'technicians' => $technicians,
        ];
    }

    /**
     * Apply dashboard filters to a Report query.
     *
     * @param  Builder<Report>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Report>
     */
    protected function applyReportFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['start_date'] ?? null), function (Builder $q) use ($filters): void {
                $q->where('created_at', '>=', Carbon::parse((string) $filters['start_date'])->startOfDay());
            })
            ->when(filled($filters['end_date'] ?? null), function (Builder $q) use ($filters): void {
                $q->where('created_at', '<=', Carbon::parse((string) $filters['end_date'])->endOfDay());
            })
            ->when(filled($filters['location_id'] ?? null), fn (Builder $q) => $q->where('location_id', $filters['location_id']))
            ->when(filled($filters['building_id'] ?? null), fn (Builder $q) => $q->where('building_id', $filters['building_id']))
            ->when(filled($filters['facility_id'] ?? null), fn (Builder $q) => $q->where('facility_id', $filters['facility_id']))
            ->when(filled($filters['status'] ?? null), fn (Builder $q) => $q->where('status', $filters['status']))
            ->when(filled($filters['officer_id'] ?? null), function (Builder $q) use ($filters): void {
                $q->whereIn('id', Assignment::query()
                    ->where('officer_id', $filters['officer_id'])
                    ->select('report_id'));
            });
    }

    /**
     * Apply dashboard filters to an Assignment query.
     *
     * @param  Builder<Assignment>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Assignment>
     */
    protected function applyAssignmentFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['start_date'] ?? null), function (Builder $q) use ($filters): void {
                $q->where('created_at', '>=', Carbon::parse((string) $filters['start_date'])->startOfDay());
            })
            ->when(filled($filters['end_date'] ?? null), function (Builder $q) use ($filters): void {
                $q->where('created_at', '<=', Carbon::parse((string) $filters['end_date'])->endOfDay());
            })
            ->when(filled($filters['officer_id'] ?? null), fn (Builder $q) => $q->where('officer_id', $filters['officer_id']))
            ->when(
                filled($filters['location_id'] ?? null)
                || filled($filters['building_id'] ?? null)
                || filled($filters['facility_id'] ?? null)
                || filled($filters['status'] ?? null),
                function (Builder $q) use ($filters): void {
                    $q->whereHas('report', function (Builder $reportQuery) use ($filters): void {
                        $reportQuery
                            ->when(filled($filters['location_id'] ?? null), fn (Builder $rq) => $rq->where('location_id', $filters['location_id']))
                            ->when(filled($filters['building_id'] ?? null), fn (Builder $rq) => $rq->where('building_id', $filters['building_id']))
                            ->when(filled($filters['facility_id'] ?? null), fn (Builder $rq) => $rq->where('facility_id', $filters['facility_id']))
                            ->when(filled($filters['status'] ?? null), fn (Builder $rq) => $rq->where('status', $filters['status']));
                    });
                }
            );
    }

    /**
     * Resolve start and end Carbon dates from filters or default month window.
     *
     * @param  array<string, mixed>  $filters
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveDateRange(array $filters, int $months): array
    {
        $endDate = filled($filters['end_date'] ?? null)
            ? Carbon::parse((string) $filters['end_date'])->endOfDay()
            : Carbon::now()->endOfMonth();

        $startDate = filled($filters['start_date'] ?? null)
            ? Carbon::parse((string) $filters['start_date'])->startOfDay()
            : $endDate->copy()->subMonths(max(1, $months) - 1)->startOfMonth();

        return [$startDate, $endDate];
    }

    /**
     * Calculate average resolution time in hours for completed assignments.
     *
     * @param  Collection<int, Assignment>  $completedAssignments
     */
    protected function calculateAverageResolutionHours(Collection $completedAssignments): float
    {
        $durations = $completedAssignments
            ->filter(fn (Assignment $assignment): bool => $assignment->completed_at !== null)
            ->map(function (Assignment $assignment): float {
                $start = $assignment->started_at ?? $assignment->created_at;

                if ($start === null || $assignment->completed_at === null) {
                    return 0.0;
                }

                return max(0.0, $start->diffInMinutes($assignment->completed_at) / 60);
            });

        if ($durations->isEmpty()) {
            return 0.0;
        }

        return round((float) $durations->avg(), 1);
    }
}
