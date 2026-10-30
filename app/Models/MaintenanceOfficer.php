<?php

namespace App\Models;

use App\Enums\OfficerAvailability;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // TODO: Add skills() after the maintenance_officer_skills table is available.
    // TODO: Add zones() after the maintenance_officer_zones table is available.
    // TODO: Add assignments() when the assignments table is defined.
}
