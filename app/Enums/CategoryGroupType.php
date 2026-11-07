<?php

namespace App\Enums;

enum CategoryGroupType: string
{
    case FacilityType = 'facility_type';
    case DamageClassification = 'damage_classification';

    // Standard Facility Type categories
    case Electrical = 'electrical';
    case Hvac = 'hvac';
    case Plumbing = 'plumbing';
    case Civil = 'civil';

    public function label(): string
    {
        return match ($this) {
            self::FacilityType => 'Tipe Fasilitas',
            self::DamageClassification => 'Klasifikasi Kerusakan',
            self::Electrical => 'Listrik',
            self::Hvac => 'HVAC',
            self::Plumbing => 'Plumbing',
            self::Civil => 'Sipil',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function facilityTypes(): array
    {
        return [
            self::Electrical->value => self::Electrical->label(),
            self::Hvac->value => self::Hvac->label(),
            self::Plumbing->value => self::Plumbing->label(),
            self::Civil->value => self::Civil->label(),
        ];
    }

    /**
     * Predefined default facility categories.
     *
     * @return array<string, string>
     */
    public static function defaultFacilityCategories(): array
    {
        return [
            'Listrik' => 'Kategori fasilitas kelistrikan, panel daya, pencahayaan, dan perkabelan.',
            'HVAC' => 'Heating, Ventilation, and Air Conditioning (pendingin ruangan, exhaust, dan sirkulasi udara).',
            'Plumbing' => 'Instalasi perpipaan, sanitasi, suplai air bersih, drainase, dan kran.',
            'Sipil' => 'Struktur bangunan fisik seperti dinding, lantai, atap, plafon, pintu, dan jendela.',
        ];
    }
}
