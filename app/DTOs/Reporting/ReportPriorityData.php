<?php

namespace App\DTOs\Reporting;

use App\Enums\PriorityLevel;
use Illuminate\Foundation\Http\FormRequest;

readonly class ReportPriorityData
{
    public function __construct(
        public string $name,
        public PriorityLevel|string $level,
        public int $slaResponseHours,
        public int $slaResolutionHours,
        public string $badgeVariant,
        public ?int $sortOrder = null,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}

    /**
     * Create a DTO instance from an associative array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $rawLevel = $data['level'] ?? PriorityLevel::Medium->value;
        $level = $rawLevel instanceof PriorityLevel
            ? $rawLevel
            : (PriorityLevel::tryFrom((string) $rawLevel) ?? PriorityLevel::Medium);

        return new self(
            name: trim((string) ($data['name'] ?? '')),
            level: $level,
            slaResponseHours: (int) ($data['sla_response_hours'] ?? 0),
            slaResolutionHours: (int) ($data['sla_resolution_hours'] ?? 0),
            badgeVariant: trim((string) ($data['badge_variant'] ?? 'info')),
            sortOrder: isset($data['sort_order']) && $data['sort_order'] !== '' ? (int) $data['sort_order'] : null,
            description: isset($data['description']) && is_string($data['description']) && trim($data['description']) !== ''
                ? trim($data['description'])
                : null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
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
            'name' => $this->name,
            'level' => $this->level instanceof PriorityLevel ? $this->level->value : (string) $this->level,
            'sla_response_hours' => $this->slaResponseHours,
            'sla_resolution_hours' => $this->slaResolutionHours,
            'badge_variant' => $this->badgeVariant,
            'sort_order' => $this->sortOrder,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
