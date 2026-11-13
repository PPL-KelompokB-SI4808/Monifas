<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Rejected = 'rejected';

    /**
     * Get the human-readable label for the report status in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Verified => 'Diverifikasi',
            self::Assigned => 'Ditugaskan',
            self::InProgress => 'Dalam Pengerjaan',
            self::Completed => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }

    /**
     * Get the badge variant name for the status.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Verified => 'info',
            self::Assigned => 'secondary',
            self::InProgress => 'primary',
            self::Completed => 'emerald',
            self::Rejected => 'danger',
        };
    }

    /**
     * Get styling classes using design tokens.
     *
     * @return array{badge: string, dot: string, border: string, bg: string, text: string}
     */
    public function styles(): array
    {
        return match ($this) {
            self::Pending => [
                'badge' => 'border-amber-500/20 bg-amber-500/10 text-amber-700 dark:text-amber-300',
                'dot' => 'bg-amber-500',
                'border' => 'border-amber-500/20',
                'bg' => 'bg-amber-500/5',
                'text' => 'text-amber-600 dark:text-amber-400',
            ],
            self::Verified => [
                'badge' => 'border-blue-500/20 bg-blue-500/10 text-blue-700 dark:text-blue-300',
                'dot' => 'bg-blue-500',
                'border' => 'border-blue-500/20',
                'bg' => 'bg-blue-500/5',
                'text' => 'text-blue-600 dark:text-blue-400',
            ],
            self::Assigned => [
                'badge' => 'border-purple-500/20 bg-purple-500/10 text-purple-700 dark:text-purple-300',
                'dot' => 'bg-purple-500',
                'border' => 'border-purple-500/20',
                'bg' => 'bg-purple-500/5',
                'text' => 'text-purple-600 dark:text-purple-400',
            ],
            self::InProgress => [
                'badge' => 'border-sky-500/20 bg-sky-500/10 text-sky-700 dark:text-sky-300',
                'dot' => 'bg-sky-500',
                'border' => 'border-sky-500/20',
                'bg' => 'bg-sky-500/5',
                'text' => 'text-sky-600 dark:text-sky-400',
            ],
            self::Completed => [
                'badge' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
                'dot' => 'bg-emerald-500',
                'border' => 'border-emerald-500/20',
                'bg' => 'bg-emerald-500/5',
                'text' => 'text-emerald-600 dark:text-emerald-400',
            ],
            self::Rejected => [
                'badge' => 'border-red-500/20 bg-red-500/10 text-red-700 dark:text-red-300',
                'dot' => 'bg-red-500',
                'border' => 'border-red-500/20',
                'bg' => 'bg-red-500/5',
                'text' => 'text-red-600 dark:text-red-400',
            ],
        };
    }

    /**
     * Get Lucide icon name representing the status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'lucide-clock',
            self::Verified => 'lucide-check-circle',
            self::Assigned => 'lucide-user-check',
            self::InProgress => 'lucide-wrench',
            self::Completed => 'lucide-check-check',
            self::Rejected => 'lucide-x-circle',
        };
    }

    /**
     * Determine if the report is waiting for supervisor verification.
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Determine if the report can be verified or rejected by a supervisor.
     */
    public function canBeVerified(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Determine if the report is verified and ready for officer assignment.
     */
    public function canBeAssigned(): bool
    {
        return $this === self::Verified;
    }

    /**
     * Determine if the report lifecycle is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this, [self::Completed, self::Rejected], true);
    }

    /**
     * Get all enum values as an array.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
