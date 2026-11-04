<?php

namespace App\Enums;

use InvalidArgumentException;

enum NotificationType: string
{
    case ReportStatusChanged = 'report_status_changed';
    case NewAssignment = 'new_assignment';
    case SlaWarning = 'sla_warning';
    case SystemAlert = 'system_alert';

    /**
     * Resolve enum instance from various string formats (snake_case, PascalCase, or name).
     */
    public static function fromString(string $value): self
    {
        $direct = self::tryFrom($value);
        if ($direct !== null) {
            return $direct;
        }

        foreach (self::cases() as $case) {
            if (strcasecmp($case->name, $value) === 0) {
                return $case;
            }
        }

        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value) ?? '');
        $converted = self::tryFrom($snake);
        if ($converted !== null) {
            return $converted;
        }

        throw new InvalidArgumentException("Invalid notification type: {$value}");
    }

    /**
     * Get human-readable label in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::ReportStatusChanged => 'Status Laporan Berubah',
            self::NewAssignment => 'Penugasan Baru',
            self::SlaWarning => 'Peringatan SLA',
            self::SystemAlert => 'Peringatan Sistem',
        };
    }

    /**
     * Get CSS badge styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::ReportStatusChanged => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
            self::NewAssignment => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
            self::SlaWarning => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
            self::SystemAlert => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        };
    }

    /**
     * Get Lucide icon name.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ReportStatusChanged => 'lucide-file-text',
            self::NewAssignment => 'lucide-user-check',
            self::SlaWarning => 'lucide-clock-alert',
            self::SystemAlert => 'lucide-alert-triangle',
        };
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

