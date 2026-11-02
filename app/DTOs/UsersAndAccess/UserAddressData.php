<?php

namespace App\DTOs\UsersAndAccess;

use Illuminate\Foundation\Http\FormRequest;

readonly class UserAddressData
{
    public function __construct(
        public int $userId,
        public string $label,
        public ?string $buildingOrUnit = null,
        public string $addressDetail = '',
        public ?string $contactPhone = null,
        public ?string $postalCode = null,
        public bool $isPrimary = false,
    ) {}

    /**
     * Create a DTO instance from an associative array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) ($data['user_id'] ?? 0),
            label: (string) ($data['label'] ?? ''),
            buildingOrUnit: isset($data['building_or_unit']) && is_string($data['building_or_unit']) && $data['building_or_unit'] !== ''
                ? $data['building_or_unit']
                : null,
            addressDetail: (string) ($data['address_detail'] ?? ''),
            contactPhone: isset($data['contact_phone']) && is_string($data['contact_phone']) && $data['contact_phone'] !== ''
                ? $data['contact_phone']
                : null,
            postalCode: isset($data['postal_code']) && is_string($data['postal_code']) && $data['postal_code'] !== ''
                ? $data['postal_code']
                : null,
            isPrimary: isset($data['is_primary']) ? (bool) $data['is_primary'] : false,
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
            'user_id' => $this->userId,
            'label' => $this->label,
            'building_or_unit' => $this->buildingOrUnit,
            'address_detail' => $this->addressDetail,
            'contact_phone' => $this->contactPhone,
            'postal_code' => $this->postalCode,
            'is_primary' => $this->isPrimary,
        ];
    }
}
