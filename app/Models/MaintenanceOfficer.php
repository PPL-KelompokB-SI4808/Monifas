<?php

namespace App\Models;

use App\Enums\OfficerAvailability;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'employee_code', 'specialization_notes', 'availability_status', 'max_active_tasks', 'is_active'])]
class MaintenanceOfficer extends Model
{
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'availability_status' => OfficerAvailability::class,
            'max_active_tasks' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user associated with the maintenance officer.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Facility categories this officer is qualified to maintain.
     *
     * @return BelongsToMany<FacilityCategory, $this>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            FacilityCategory::class,
            'maintenance_officer_skills',
            'maintenance_officer_id',
            'facility_category_id'
        )->withTimestamps();
    }

    /**
     * Locations and zones covered by this officer.
     *
     * @return BelongsToMany<Location, $this>
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'maintenance_officer_zones',
            'maintenance_officer_id',
            'location_id'
        )->withPivot('area_zone_id')
            ->withTimestamps();
    }

    /**
     * Assignments handled by this officer.
     *
     * @return HasMany<Assignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'officer_id');
    }
}
