<?php

namespace App\Models;

use App\Enums\PriorityLevel;
use Database\Factories\ReportPriorityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportPriority extends Model
{
    /** @use HasFactory<ReportPriorityFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_priorities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'sla_response_hours',
        'sla_resolution_hours',
        'badge_variant',
        'sort_order',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => PriorityLevel::class,
            'sla_response_hours' => 'integer',
            'sla_resolution_hours' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to order priorities by urgency (from Emergency to Low).
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOrderedByUrgency(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
