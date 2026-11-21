<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Ended = 'ended';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable label for the campaign status in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Terjadwal',
            self::Active => 'Aktif',
            self::Ended => 'Berakhir',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get the badge variant name for the status.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Scheduled => 'warning',
            self::Active => 'emerald',
            self::Ended => 'secondary',
            self::Cancelled => 'danger',
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
