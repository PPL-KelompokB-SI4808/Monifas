<?php

namespace App\Services\Maintenance\Assignments;

use App\Enums\AssignmentStatus;
use App\Enums\NotificationType;
use App\Enums\OfficerAvailability;
use App\Enums\ReportStatus;
use App\Models\Assignment;
use App\Models\MaintenanceOfficer;
use App\Models\Report;
use App\Models\User;
use App\Services\UsersAndAccess\Users\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AssignmentService
{
    /**
     * Create a new AssignmentService instance.
     */
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * Recommend active officers whose skills and coverage match a report.
     *
     * @return Collection<int, MaintenanceOfficer>
     */
    public function recommendOfficersForReport(Report $report): Collection
    {
        $report->loadMissing([
            'facility:id,facility_category_id',
            'building:id,area_zone_id',
        ]);

        $facilityCategoryId = $report->facility?->facility_category_id;
        $areaZoneId = $report->building?->area_zone_id;

        return MaintenanceOfficer::query()
            ->where('is_active', true)
            ->where('availability_status', OfficerAvailability::Available->value)
            ->whereHas('skills', function (Builder $query) use ($facilityCategoryId): void {
                $query->where('facility_categories.id', $facilityCategoryId);
            })
            ->whereHas('zones', function (Builder $query) use ($report, $areaZoneId): void {
                $query->where('locations.id', $report->location_id)
                    ->where(function (Builder $query) use ($areaZoneId): void {
                        $query->whereNull('maintenance_officer_zones.area_zone_id')
                            ->orWhere('maintenance_officer_zones.area_zone_id', $areaZoneId);
                    });
            })
            ->withCount([
                'assignments as active_tasks_count' => function (Builder $query): void {
                    $query->whereIn('status', [
                        AssignmentStatus::Assigned->value,
                        AssignmentStatus::Accepted->value,
                        AssignmentStatus::InProgress->value,
                    ]);
                },
            ])
            ->havingRaw('active_tasks_count < maintenance_officers.max_active_tasks')
            ->orderBy('active_tasks_count')
            ->get();
    }

    /**
     * Calculate response and resolution SLA deadlines for a report.
     *
     * @return array{response_due_at: Carbon, resolution_due_at: Carbon}
     */
    public function calculateSlaDeadlines(Report $report): array
    {
        $report->loadMissing('priority');
        $priority = $report->priority;

        if ($priority === null) {
            throw new InvalidArgumentException('Prioritas SLA pada laporan tidak ditemukan.');
        }

        return [
            'response_due_at' => Carbon::now()->addHours($priority->sla_response_hours),
            'resolution_due_at' => Carbon::now()->addHours($priority->sla_resolution_hours),
        ];
    }

    /**
     * Assign a verified report to an active maintenance officer.
     *
     * @throws InvalidArgumentException
     */
    public function assignReport(
        Report $report,
        MaintenanceOfficer|int $officer,
        User $assignedBy,
        ?string $instructionNotes = null
    ): Assignment {
        return DB::transaction(function () use ($report, $officer, $assignedBy, $instructionNotes): Assignment {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->find($report->id);

            if ($lockedReport === null) {
                throw new InvalidArgumentException('Laporan yang akan ditugaskan tidak ditemukan.');
            }

            if (! $lockedReport->status->canBeAssigned()) {
                throw new InvalidArgumentException('Hanya laporan berstatus diverifikasi yang dapat ditugaskan.');
            }

            $resolvedOfficer = $this->resolveActiveOfficer($officer);
            $deadlines = $this->calculateSlaDeadlines($lockedReport);

            $assignment = Assignment::create([
                'report_id' => $lockedReport->id,
                'officer_id' => $resolvedOfficer->id,
                'assigned_by' => $assignedBy->id,
                'status' => AssignmentStatus::Assigned,
                'sla_response_due_at' => $deadlines['response_due_at'],
                'sla_resolution_due_at' => $deadlines['resolution_due_at'],
                'instruction_notes' => $instructionNotes,
            ]);

            $lockedReport->update(['status' => ReportStatus::Assigned]);

            // TODO: Set the action URL after assignment routes are available.
            $actionUrl = null;

            $this->notificationService->send(
                user: $resolvedOfficer->user_id,
                type: NotificationType::NewAssignment,
                title: 'Penugasan Baru',
                message: "Anda menerima penugasan untuk laporan #{$lockedReport->ticket_number}.",
                actionUrl: $actionUrl,
            );

            $this->notificationService->send(
                user: $lockedReport->reporter_id,
                type: NotificationType::ReportStatusChanged,
                title: 'Status Laporan Berubah',
                message: "Laporan kerusakan #{$lockedReport->ticket_number} telah ditugaskan.",
                actionUrl: $actionUrl,
            );

            return $assignment;
        });
    }

    /**
     * Reassign an active assignment to another active officer.
     *
     * @throws InvalidArgumentException
     */
    public function reassignAssignment(
        Assignment $assignment,
        MaintenanceOfficer|int $newOfficer,
        string $reason,
        User $reassignedBy
    ): Assignment {
        return DB::transaction(function () use ($assignment, $newOfficer, $reason, $reassignedBy): Assignment {
            $lockedAssignment = Assignment::query()
                ->lockForUpdate()
                ->find($assignment->id);

            if ($lockedAssignment === null) {
                throw new InvalidArgumentException('Penugasan tidak ditemukan.');
            }

            if (in_array($lockedAssignment->status, [AssignmentStatus::Completed, AssignmentStatus::Cancelled], true)) {
                throw new InvalidArgumentException('Penugasan yang sudah selesai atau dibatalkan tidak dapat dialihkan.');
            }

            $report = $lockedAssignment->report;

            if ($report === null) {
                throw new InvalidArgumentException('Laporan pada penugasan tidak ditemukan.');
            }

            $trimmedReason = trim($reason);
            if ($trimmedReason === '') {
                throw new InvalidArgumentException('Alasan pengalihan penugasan wajib diisi.');
            }

            $resolvedOfficer = $this->resolveActiveOfficer($newOfficer);
            $deadlines = $this->calculateSlaDeadlines($report);

            $lockedAssignment->update([
                'status' => AssignmentStatus::Reassigned,
                'rejection_or_cancel_reason' => $trimmedReason,
            ]);

            $newAssignment = Assignment::create([
                'report_id' => $report->id,
                'officer_id' => $resolvedOfficer->id,
                'assigned_by' => $reassignedBy->id,
                'status' => AssignmentStatus::Assigned,
                'sla_response_due_at' => $deadlines['response_due_at'],
                'sla_resolution_due_at' => $deadlines['resolution_due_at'],
            ]);

            $report->update(['status' => ReportStatus::Assigned]);

            // TODO: Set the action URL after assignment routes are available.
            $actionUrl = null;

            $this->notificationService->send(
                user: $resolvedOfficer->user_id,
                type: NotificationType::NewAssignment,
                title: 'Penugasan Baru',
                message: "Laporan kerusakan #{$report->ticket_number} dialihkan kepada Anda oleh {$reassignedBy->name}.",
                actionUrl: $actionUrl,
            );

            return $newAssignment;
        });
    }

    /**
     * Cancel an assignment and return its report to verified status.
     *
     * @throws InvalidArgumentException
     */
    public function cancelAssignment(Assignment $assignment, string $reason, User $cancelledBy): Assignment
    {
        return DB::transaction(function () use ($assignment, $reason, $cancelledBy): Assignment {
            $lockedAssignment = Assignment::query()
                ->lockForUpdate()
                ->find($assignment->id);

            if ($lockedAssignment === null) {
                throw new InvalidArgumentException('Penugasan tidak ditemukan.');
            }

            if (in_array($lockedAssignment->status, [AssignmentStatus::Completed, AssignmentStatus::Cancelled], true)) {
                throw new InvalidArgumentException('Penugasan yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi.');
            }

            $report = $lockedAssignment->report;

            if ($report === null) {
                throw new InvalidArgumentException('Laporan pada penugasan tidak ditemukan.');
            }

            $trimmedReason = trim($reason);
            if ($trimmedReason === '') {
                throw new InvalidArgumentException('Alasan pembatalan penugasan wajib diisi.');
            }

            $lockedAssignment->update([
                'status' => AssignmentStatus::Cancelled,
                'rejection_or_cancel_reason' => $trimmedReason,
            ]);

            $report->update(['status' => ReportStatus::Verified]);

            $officer = $lockedAssignment->officer;
            if ($officer === null) {
                throw new InvalidArgumentException('Petugas pada penugasan tidak ditemukan.');
            }

            // TODO: Set the action URL after assignment routes are available.
            $actionUrl = null;

            $this->notificationService->send(
                user: $officer->user_id,
                type: NotificationType::ReportStatusChanged,
                title: 'Penugasan Dibatalkan',
                message: "Penugasan laporan #{$report->ticket_number} dibatalkan oleh {$cancelledBy->name}: {$trimmedReason}",
                actionUrl: $actionUrl,
            );

            $this->notificationService->send(
                user: $report->reporter_id,
                type: NotificationType::ReportStatusChanged,
                title: 'Status Laporan Berubah',
                message: "Penugasan untuk laporan #{$report->ticket_number} dibatalkan. Laporan kembali menunggu penugasan.",
                actionUrl: $actionUrl,
            );

            return $lockedAssignment;
        });
    }

    /**
     * Get paginated assignments with optional status and officer filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Assignment>
     */
    public function paginateAssignments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Assignment::query()
            ->with(['report', 'officer.user', 'assignedBy'])
            ->when(isset($filters['status']) && $filters['status'] !== '', function (Builder $query) use ($filters): void {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['officer_id']) && $filters['officer_id'] !== '', function (Builder $query) use ($filters): void {
                $query->where('officer_id', $filters['officer_id']);
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Resolve an officer and ensure that the record is active.
     *
     * @throws InvalidArgumentException
     */
    private function resolveActiveOfficer(MaintenanceOfficer|int $officer): MaintenanceOfficer
    {
        $officerId = $officer instanceof MaintenanceOfficer ? $officer->id : $officer;
        $resolvedOfficer = MaintenanceOfficer::query()->find($officerId);

        if ($resolvedOfficer === null) {
            throw new InvalidArgumentException('Petugas yang dipilih tidak ditemukan.');
        }

        if (! $resolvedOfficer->is_active) {
            throw new InvalidArgumentException('Petugas yang dipilih tidak aktif dan tidak dapat menerima penugasan.');
        }

        return $resolvedOfficer;
    }
}
