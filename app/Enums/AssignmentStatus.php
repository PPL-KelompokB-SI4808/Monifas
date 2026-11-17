<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Assigned = 'assigned';
    case Accepted = 'accepted';
    case RejectedByOfficer = 'rejected_by_officer';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Reassigned = 'reassigned';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable label for the assignment status in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Ditugaskan',
            self::Accepted => 'Diterima',
            self::RejectedByOfficer => 'Ditolak Petugas',
            self::InProgress => 'Dalam Pengerjaan',
            self::Completed => 'Selesai',
            self::Reassigned => 'Dialihkan',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get the badge variant name for the status.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Assigned => 'secondary',
            self::Accepted => 'info',
            self::RejectedByOfficer => 'danger',
            self::InProgress => 'primary',
            self::Completed => 'emerald',
            self::Reassigned => 'warning',
            self::Cancelled => 'danger',
        };
    }
}
