<?php

namespace App\Enums;

enum FacilityCondition: string
{
    case Good = 'good';
    case MinorDamage = 'minor_damage';
    case MajorDamage = 'major_damage';
    case OutOfOrder = 'out_of_order';

    public function label(): string
    {
        return match(\) {
            self::Good => 'Baik',
            self::MinorDamage => 'Rusak Ringan',
            self::MajorDamage => 'Rusak Berat',
            self::OutOfOrder => 'Tidak Berfungsi',
        };
    }
}
