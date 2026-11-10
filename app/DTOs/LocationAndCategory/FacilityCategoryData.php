<?php

namespace App\DTOs\LocationAndCategory;

use Illuminate\Foundation\Http\FormRequest;

readonly class FacilityCategoryData
{
    public function __construct(
        public string $code,
        public string $name,
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
        return new self(
            code: trim((string) ($data['code'] ?? '')),
            name: trim((string) ($data['name'] ?? '')),
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
     * Convert DTO to array for model persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
