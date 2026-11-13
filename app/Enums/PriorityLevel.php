<?php

namespace App\Enums;

enum PriorityLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Emergency = 'emergency';

    /**
     * Get the human-readable label for the priority level.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
            self::Emergency => 'Darurat',
        };
    }

    /**
     * Default badge variant for the priority level.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Low => 'secondary',
            self::Medium => 'info',
            self::High => 'warning',
            self::Emergency => 'danger',
        };
    }

    /**
     * Default sort order index from highest urgency (Emergency = 1) to lowest (Low = 4).
     */
    public function defaultSortOrder(): int
    {
        return match ($this) {
            self::Emergency => 1,
            self::High => 2,
            self::Medium => 3,
            self::Low => 4,
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
