<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Database\Factories\MaintenanceCampaignFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceCampaign extends Model
{
    /** @use HasFactory<MaintenanceCampaignFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'maintenance_campaigns';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'created_by',
        'building_id',
        'facility_category_id',
        'title',
        'objective',
        'content',
        'banner_path',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => CampaignStatus::class,
        ];
    }

    /**
     * Get the user who created the campaign.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for creator relation.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->creator();
    }

    /**
     * Get the building targeted by the campaign.
     *
     * @return BelongsTo<Building, $this>
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    /**
     * Get the facility category targeted by the campaign.
     *
     * @return BelongsTo<FacilityCategory, $this>
     */
    public function facilityCategory(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'facility_category_id');
    }

    /**
     * Scope a query to only include active campaigns.
     *
     * @param  Builder<MaintenanceCampaign>  $query
     * @return Builder<MaintenanceCampaign>
     */
    public function scopeActive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('status', CampaignStatus::Active)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }
}
