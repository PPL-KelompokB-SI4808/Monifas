<?php

namespace App\Services\Reporting\Reports;

use App\DTOs\Reporting\ReportData;
use App\Enums\NotificationType;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use App\Services\UsersAndAccess\Users\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;

class ReportService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Generate unique automatic ticket number with format: MNF-YYYYMMDD-XXXX using Carbon.
     */
    public function generateTicketNumber(?Carbon $date = null): string
    {
        $carbonDate = $date ?? Carbon::now();
        $datePrefix = 'MNF-'.$carbonDate->format('Ymd').'-';

        $lastTicket = Report::query()
            ->where('ticket_number', 'like', $datePrefix.'%')
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $nextSequence = 1;
        if ($lastTicket && preg_match('/^'.preg_quote($datePrefix, '/').'(\d{4})$/', $lastTicket, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        $ticketNumber = $datePrefix.sprintf('%04d', $nextSequence);

        while (Report::query()->where('ticket_number', $ticketNumber)->exists()) {
            $nextSequence++;
            $ticketNumber = $datePrefix.sprintf('%04d', $nextSequence);
        }

        return $ticketNumber;
    }

    /**
     * Store a new report safely inside a database transaction.
     */
    public function store(ReportData $dto, User|int $reporter): Report
    {
        return DB::transaction(function () use ($dto, $reporter) {
            $reporterId = $reporter instanceof User ? $reporter->id : $reporter;
            $ticketNumber = $this->generateTicketNumber();

            $attributes = $dto->toArray();
            $attributes['ticket_number'] = $ticketNumber;
            $attributes['reporter_id'] = $reporterId;
            $attributes['status'] = ReportStatus::Pending->value;
            $attributes['verified_by'] = null;
            $attributes['verified_at'] = null;
            $attributes['rejection_reason'] = null;
            $attributes['completed_at'] = null;

            return Report::create($attributes);
        });
    }

    /**
     * Update an existing pending report.
     *
     * @throws InvalidArgumentException
     */
    public function update(Report $report, ReportData $dto): Report
    {
        if ($report->status !== ReportStatus::Pending) {
            throw new InvalidArgumentException('Hanya laporan berstatus menunggu verifikasi (Pending) yang dapat diperbarui.');
        }

        return DB::transaction(function () use ($report, $dto) {
            $attributes = $dto->toArray();
            unset($attributes['ticket_number'], $attributes['status']);

            $report->update($attributes);

            return $report->fresh(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority']);
        });
    }

    /**
     * Supervisor verification: Approve (Verify) report and adjust SLA priority if specified.
     *
     * @throws InvalidArgumentException
     */
    public function verify(Report $report, User|int $supervisor, ?int $adjustedPriorityId = null): Report
    {
        if ($report->status !== ReportStatus::Pending) {
            throw new InvalidArgumentException('Hanya laporan berstatus menunggu verifikasi yang dapat disetujui.');
        }

        if ($adjustedPriorityId !== null) {
            $priorityExists = ReportPriority::query()->where('id', $adjustedPriorityId)->exists();
            if (! $priorityExists) {
                throw new InvalidArgumentException('Prioritas SLA yang dipilih tidak valid.');
            }
        }

        return DB::transaction(function () use ($report, $supervisor, $adjustedPriorityId) {
            $supervisorId = $supervisor instanceof User ? $supervisor->id : $supervisor;

            $updateData = [
                'status' => ReportStatus::Verified,
                'verified_by' => $supervisorId,
                'verified_at' => Carbon::now(),
                'rejection_reason' => null,
            ];

            if ($adjustedPriorityId !== null) {
                $updateData['priority_id'] = $adjustedPriorityId;
            }

            $report->update($updateData);

            $actionUrl = Route::has('reporting.reports.show')
                ? route('reporting.reports.show', $report)
                : null;

            $this->notificationService->send(
                user: $report->reporter_id,
                type: NotificationType::ReportStatusChanged,
                title: 'Laporan Diverifikasi',
                message: "Laporan kerusakan #{$report->ticket_number} telah diverifikasi oleh supervisor.",
                actionUrl: $actionUrl,
            );

            return $report->fresh(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority', 'verifiedBy']);
        });
    }

    /**
     * Supervisor verification: Reject report with reason and notify the reporter.
     *
     * @throws InvalidArgumentException
     */
    public function reject(Report $report, User|int $supervisor, string $rejectionReason): Report
    {
        if ($report->status !== ReportStatus::Pending) {
            throw new InvalidArgumentException('Hanya laporan berstatus menunggu verifikasi yang dapat ditolak.');
        }

        $trimmedReason = trim($rejectionReason);
        if ($trimmedReason === '') {
            throw new InvalidArgumentException('Alasan penolakan laporan wajib diisi.');
        }

        return DB::transaction(function () use ($report, $supervisor, $trimmedReason) {
            $supervisorId = $supervisor instanceof User ? $supervisor->id : $supervisor;

            $report->update([
                'status' => ReportStatus::Rejected,
                'verified_by' => $supervisorId,
                'verified_at' => Carbon::now(),
                'rejection_reason' => $trimmedReason,
            ]);

            $actionUrl = Route::has('reporting.reports.show')
                ? route('reporting.reports.show', $report)
                : null;

            $this->notificationService->send(
                user: $report->reporter_id,
                type: NotificationType::ReportStatusChanged,
                title: 'Laporan Ditolak',
                message: "Laporan kerusakan #{$report->ticket_number} ditolak: {$trimmedReason}",
                actionUrl: $actionUrl,
            );

            return $report->fresh(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority', 'verifiedBy']);
        });
    }

    /**
     * Generic handler for supervisor verification from request inputs.
     *
     * @throws InvalidArgumentException
     */
    public function handleVerification(
        Report $report,
        User|int $supervisor,
        string $action,
        ?string $rejectionReason = null,
        ?int $adjustedPriorityId = null
    ): Report {
        return match (strtolower(trim($action))) {
            'verify', 'approve' => $this->verify($report, $supervisor, $adjustedPriorityId),
            'reject' => $this->reject($report, $supervisor, (string) $rejectionReason),
            default => throw new InvalidArgumentException("Aksi verifikasi '{$action}' tidak dikenal."),
        };
    }

    /**
     * Delete an unverified (Pending) report safely.
     *
     * @throws InvalidArgumentException
     */
    public function delete(Report $report): bool
    {
        if ($report->status !== ReportStatus::Pending) {
            throw new InvalidArgumentException('Hanya laporan berstatus menunggu verifikasi (Pending) yang dapat dihapus.');
        }

        return DB::transaction(function () use ($report) {
            return (bool) $report->delete();
        });
    }

    /**
     * Get paginated list of reports with filters.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Report>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Report::query()
            ->with(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority', 'verifiedBy'])
            ->when($filters['search'] ?? null, function (Builder $query, $search) {
                $search = trim((string) $search);
                $query->where(function (Builder $q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function (Builder $query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['priority_id']) && $filters['priority_id'] !== '', function (Builder $query) use ($filters) {
                $query->where('priority_id', $filters['priority_id']);
            })
            ->when(isset($filters['location_id']) && $filters['location_id'] !== '', function (Builder $query) use ($filters) {
                $query->where('location_id', $filters['location_id']);
            })
            ->when(isset($filters['building_id']) && $filters['building_id'] !== '', function (Builder $query) use ($filters) {
                $query->where('building_id', $filters['building_id']);
            })
            ->when(isset($filters['damage_category_id']) && $filters['damage_category_id'] !== '', function (Builder $query) use ($filters) {
                $query->where('damage_category_id', $filters['damage_category_id']);
            })
            ->when(isset($filters['reporter_id']) && $filters['reporter_id'] !== '', function (Builder $query) use ($filters) {
                $query->where('reporter_id', $filters['reporter_id']);
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find a report by its ID.
     */
    public function findById(int $id): ?Report
    {
        return Report::query()
            ->with(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority', 'verifiedBy', 'repairs'])
            ->find($id);
    }

    /**
     * Find a report by its ticket number.
     */
    public function findByTicketNumber(string $ticketNumber): ?Report
    {
        return Report::query()
            ->with(['reporter', 'location', 'building', 'room', 'facility', 'damageCategory', 'priority', 'verifiedBy', 'repairs'])
            ->where('ticket_number', trim($ticketNumber))
            ->first();
    }
}
