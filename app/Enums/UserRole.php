<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case FacilityUser = 'facility_user';
    case Technician = 'technician';
    case Supervisor = 'supervisor';
    case Management = 'management';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::FacilityUser => 'Facility User',
            self::Technician => 'Technician',
            self::Supervisor => 'Supervisor',
            self::Management => 'Management',
        };
    }
}
