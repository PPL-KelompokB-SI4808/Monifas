<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property AssignmentStatus $status
 * @property Carbon $sla_response_due_at
 * @property Carbon $sla_resolution_due_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 */
#[Fillable([
    'report_id',
    'maintenance_schedule_id',
    'officer_id',
    'assigned_by',
    'status',
    'sla_response_due_at',
    'sla_resolution_due_at',
    'accepted_at',
    'started_at',
    'completed_at',
    'rejection_or_cancel_reason',
    'instruction_notes',
])]
class Assignment extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
            'sla_response_due_at' => 'datetime',
            'sla_resolution_due_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Report associated with this assignment.
     *
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Maintenance officer assigned to the report.
     *
     * @return BelongsTo<MaintenanceOfficer, $this>
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOfficer::class, 'officer_id');
    }

    /**
     * User who created this assignment.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Repair associated with this assignment.
     *
     * @return HasOne<Repair, $this>
     */
    public function repair(): HasOne
    {
        return $this->hasOne(Repair::class, 'assignment_id');
    }

    // TODO: Add schedule() after the maintenance_schedules table is created in PBI-MNT-04.
}
