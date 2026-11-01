<?php

namespace App\DTOs\Facility;

class BuildingData
{
    public function __construct(
        public readonly int $locationId,
        public readonly ?int $areaZoneId,
        public readonly string $code,
        public readonly string $name,
        public readonly int $totalFloors,
        public readonly ?string $description,
        public readonly bool $isActive
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            locationId: (int) $request->validated('location_id'),
            areaZoneId: $request->validated('area_zone_id') ? (int) $request->validated('area_zone_id') : null,
            code: $request->validated('code'),
            name: $request->validated('name'),
            totalFloors: (int) $request->validated('total_floors'),
            description: $request->validated('description'),
            isActive: (bool) $request->validated('is_active', true)
        );
    }

    public function toArray(): array
    {
        return [
            'location_id' => $this->locationId,
            'area_zone_id' => $this->areaZoneId,
            'code' => $this->code,
            'name' => $this->name,
            'total_floors' => $this->totalFloors,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
