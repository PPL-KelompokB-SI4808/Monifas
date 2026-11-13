<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Carbon\Carbon;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $ticket_number
 * @property int $reporter_id
 * @property int $location_id
 * @property int $building_id
 * @property int|null $room_id
 * @property int $facility_id
 * @property int $damage_category_id
 * @property int $priority_id
 * @property string $title
 * @property string $description
 * @property ReportStatus $status
 * @property int|null $verified_by
 * @property Carbon|null $verified_at
 * @property string|null $rejection_reason
 * @property Carbon|null $completed_at
 */
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'location_id',
        'building_id',
        'room_id',
        'facility_id',
        'damage_category_id',
        'priority_id',
        'title',
        'description',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'verified_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * User who submitted the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Supervisor or Admin who verified or rejected the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Location area of the report.
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Building of the reported facility.
     *
     * @return BelongsTo<Building, $this>
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Room where the facility is located (if applicable).
     *
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Physical facility being reported.
     *
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Damage category classification.
     *
     * @return BelongsTo<DamageCategory, $this>
     */
    public function damageCategory(): BelongsTo
    {
        return $this->belongsTo(DamageCategory::class);
    }

    /**
     * SLA Report Priority configuration.
     *
     * @return BelongsTo<ReportPriority, $this>
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(ReportPriority::class, 'priority_id');
    }

    /**
     * Repair records associated with this report.
     *
     * @return HasMany<Repair, $this>
     */
    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class, 'report_id');
    }

    /**
     * Scope query to only pending reports.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::Pending);
    }

    /**
     * Scope query to only verified reports.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::Verified);
    }

    /**
     * Scope query to open reports (not completed and not rejected).
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ReportStatus::Completed, ReportStatus::Rejected]);
    }
}
