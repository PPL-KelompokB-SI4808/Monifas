<?php

namespace App\DTOs\Supporting;

use App\Enums\AnnouncementStatus;
use Illuminate\Foundation\Http\FormRequest;

readonly class AnnouncementData
{
    public function __construct(
        public string $title,
        public string $content,
        public string $category,
        public ?int $createdBy = null,
        public ?string $targetRole = null,
        public ?int $targetBuildingId = null,
        public ?string $publishAt = null,
        public ?string $expiresAt = null,
        public AnnouncementStatus|string $status = AnnouncementStatus::Draft,
    ) {}

    /**
     * Create a DTO instance from an associative array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $status = $data['status'] ?? AnnouncementStatus::Draft;
        if (is_string($status)) {
            $status = AnnouncementStatus::tryFrom($status) ?? $status;
        }

        return new self(
            title: (string) ($data['title'] ?? ''),
            content: (string) ($data['content'] ?? ''),
            category: (string) ($data['category'] ?? ''),
            createdBy: isset($data['created_by']) ? (int) $data['created_by'] : null,
            targetRole: ! empty($data['target_role']) ? (string) $data['target_role'] : null,
            targetBuildingId: ! empty($data['target_building_id']) ? (int) $data['target_building_id'] : null,
            publishAt: ! empty($data['publish_at']) ? (string) $data['publish_at'] : null,
            expiresAt: ! empty($data['expires_at']) ? (string) $data['expires_at'] : null,
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

        if (! isset($validated['created_by']) && $request->user()) {
            $validated['created_by'] = $request->user()->id;
        }

        return self::fromArray($validated);
    }

    /**
     * Export DTO properties to array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $statusValue = $this->status instanceof AnnouncementStatus
            ? $this->status->value
            : $this->status;

        $payload = [
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'target_role' => $this->targetRole,
            'target_building_id' => $this->targetBuildingId,
            'publish_at' => $this->publishAt,
            'expires_at' => $this->expiresAt,
            'status' => $statusValue,
        ];

        if ($this->createdBy !== null) {
            $payload['created_by'] = $this->createdBy;
        }

        return $payload;
    }
}

