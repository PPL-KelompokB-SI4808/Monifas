<?php

namespace App\Models;

use App\Enums\RepairExecutionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOfficer::class, 'officer_id');
    }

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
