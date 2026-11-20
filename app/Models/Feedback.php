<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $report_id
 * @property int|null $repair_id
 * @property int $user_id
 * @property int $rating
 * @property string $comment
 * @property string|null $admin_response
 * @property int|null $responded_by
 * @property Carbon|null $responded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Report $report
 * @property-read Repair|null $repair
 * @property-read User $user
 * @property-read User|null $responder
 */
class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'report_id',
        'repair_id',
        'user_id',
        'rating',
        'comment',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Determine whether the feedback is locked for editing by user.
     * Locked when an admin has responded.
     */
    public function isLocked(): bool
    {
        return $this->admin_response !== null;
    }

    /**
     * Get the report associated with the feedback.
     *
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the repair record associated with the feedback.
     *
     * @return BelongsTo<Repair, $this>
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    /**
     * Get the user who submitted the feedback.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user who responded to the feedback.
     *
     * @return BelongsTo<User, $this>
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
