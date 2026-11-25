<?php

namespace App\Models;

use App\Enums\RepairExecutionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $assignment_id
 * @property int|null $report_id
 * @property int $facility_id
 * @property int $officer_id
 * @property Carbon $started_at
 * @property Carbon|null $ended_at
 * @property string $technical_action
 * @property string|null $repair_result
 * @property string $resulting_facility_condition
 * @property RepairExecutionStatus $status
 */
class Repair extends Model
{
    protected $fillable = [
        'assignment_id',
        'report_id',
        'facility_id',
        'officer_id',
        'started_at',
        'ended_at',
        'technical_action',
        'repair_result',
        'resulting_facility_condition',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'status' => RepairExecutionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Assignment, $this>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * @return BelongsTo<MaintenanceOfficer, $this>
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOfficer::class, 'officer_id');
    }

    /**
     * @return HasMany<RepairMaterial, $this>
     */
    public function materials(): HasMany
    {
        return $this->hasMany(RepairMaterial::class);
    }

    public function cost(): HasOne
    {
        return $this->hasOne(RepairCost::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    public function isCompleteForClosure(): bool
    {
        return $this->status === RepairExecutionStatus::Completed
            && $this->ended_at !== null
            && filled($this->repair_result);
    }
}
