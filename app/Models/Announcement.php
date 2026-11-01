<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
use App\Enums\UserRole;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'created_by',
    'title',
    'content',
    'category',
    'target_role',
    'target_building_id',
    'publish_at',
    'expires_at',
    'status',
])]
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'created_by',
        'title',
        'content',
        'category',
        'target_role',
        'target_building_id',
        'publish_at',
        'expires_at',
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
            'publish_at' => 'datetime',
            'expires_at' => 'datetime',
            'status' => AnnouncementStatus::class,
        ];
    }

    /**
     * Get the user who created the announcement.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the target building for the announcement.
     *
     * @return BelongsTo<Building, $this>
     */
    public function targetBuilding(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'target_building_id');
    }

    /**
     * Scope a query to only include active announcements relevant to the given user.
     *
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    public function scopeActiveForUser(Builder $query, User $user): Builder
    {
        $now = now();
        $roleSlug = $user->role?->slug ?? (is_string($user->role) ? $user->role : null);
        $isAdmin = $user->hasRole(UserRole::Admin) || $roleSlug === 'admin';

        return $query->where('status', AnnouncementStatus::Published)
            ->where('publish_at', '<=', $now)
            ->where('expires_at', '>=', $now)
            ->when(! $isAdmin, function (Builder $q) use ($roleSlug, $user) {
                $q->where(function (Builder $roleQuery) use ($roleSlug) {
                    $roleQuery->whereNull('target_role');
                    if ($roleSlug) {
                        $roleQuery->orWhere('target_role', $roleSlug);
                    }
                })->where(function (Builder $buildingQuery) use ($user) {
                    $buildingQuery->whereNull('target_building_id');
                    if (! empty($user->building_id)) {
                        $buildingQuery->orWhere('target_building_id', $user->building_id);
                    }
                });
            });
    }
}