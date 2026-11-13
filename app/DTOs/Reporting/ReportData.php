<?php

namespace App\DTOs\Reporting;

use App\Enums\ReportStatus;
use Illuminate\Foundation\Http\FormRequest;

readonly class ReportData
{
    public function __construct(
        public string $title,
        public string $description,
        public int $locationId,
        public int $buildingId,
        public ?int $roomId,
        public int $facilityId,
        public int $damageCategoryId,
        public int $priorityId,
        public ReportStatus $status = ReportStatus::Pending,
    ) {}

    /**
     * Create a DTO instance from an associative array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $rawStatus = $data['status'] ?? ReportStatus::Pending->value;
        $status = $rawStatus instanceof ReportStatus
            ? $rawStatus
            : (ReportStatus::tryFrom((string) $rawStatus) ?? ReportStatus::Pending);

        return new self(
            title: trim((string) ($data['title'] ?? '')),
            description: trim((string) ($data['description'] ?? '')),
            locationId: (int) ($data['location_id'] ?? 0),
            buildingId: (int) ($data['building_id'] ?? 0),
            roomId: isset($data['room_id']) && $data['room_id'] !== '' && $data['room_id'] !== null
                ? (int) $data['room_id']
                : null,
            facilityId: (int) ($data['facility_id'] ?? 0),
            damageCategoryId: (int) ($data['damage_category_id'] ?? 0),
            priorityId: (int) ($data['priority_id'] ?? 0),
            status: $status,
        );
    }

    /**
     * Create a DTO instance from a validated Form Request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        return self::fromArray($validated);
    }

    /**
     * Export DTO properties to array format for model persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'location_id' => $this->locationId,
            'building_id' => $this->buildingId,
            'room_id' => $this->roomId,
            'facility_id' => $this->facilityId,
            'damage_category_id' => $this->damageCategoryId,
            'priority_id' => $this->priorityId,
            'status' => $this->status->value,
        ];
    }
}
