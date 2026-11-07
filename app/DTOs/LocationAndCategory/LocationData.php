<?php

namespace App\DTOs\LocationAndCategory;

use Illuminate\Foundation\Http\FormRequest;

readonly class LocationData
{
    public function __construct(
        public ?int $parentId,
        public string $code,
        public string $name,
        public string $region,
        public string $address,
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
            parentId: ! empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            region: (string) ($data['region'] ?? ''),
            address: (string) ($data['address'] ?? ''),
            description: isset($data['description']) && is_string($data['description']) && $data['description'] !== ''
                ? $data['description']
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
            'parent_id' => $this->parentId,
            'code' => $this->code,
            'name' => $this->name,
            'region' => $this->region,
            'address' => $this->address,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
