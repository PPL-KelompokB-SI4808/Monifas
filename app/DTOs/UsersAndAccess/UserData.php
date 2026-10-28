<?php

namespace App\DTOs\UsersAndAccess;

use Illuminate\Foundation\Http\FormRequest;

readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?int $roleId = null,
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
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            password: isset($data['password']) && is_string($data['password']) && $data['password'] !== '' ? $data['password'] : null,
            roleId: isset($data['role_id']) ? (int) $data['role_id'] : null,
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
     * Export DTO properties to array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->roleId,
            'is_active' => $this->isActive,
        ];

        if ($this->password !== null) {
            $payload['password'] = $this->password;
        }

        return $payload;
    }
}
