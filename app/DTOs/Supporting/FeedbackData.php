<?php

namespace App\DTOs\Supporting;

use Illuminate\Foundation\Http\FormRequest;

readonly class FeedbackData
{
    public function __construct(
        public int $reportId,
        public int $userId,
        public int $rating,
        public string $comment,
        public ?int $repairId = null,
        public ?string $adminResponse = null,
        public ?int $respondedBy = null,
        public ?string $respondedAt = null,
    ) {}

    /**
     * Create a DTO instance from an associative array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reportId: (int) ($data['report_id'] ?? 0),
            userId: (int) ($data['user_id'] ?? 0),
            rating: (int) ($data['rating'] ?? 0),
            comment: (string) ($data['comment'] ?? ''),
            repairId: isset($data['repair_id']) && $data['repair_id'] !== '' ? (int) $data['repair_id'] : null,
            adminResponse: ! empty($data['admin_response']) ? (string) $data['admin_response'] : null,
            respondedBy: isset($data['responded_by']) && $data['responded_by'] !== '' ? (int) $data['responded_by'] : null,
            respondedAt: ! empty($data['responded_at']) ? (string) $data['responded_at'] : null,
        );
    }

    /**
     * Create a DTO instance from a validated Form Request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();
        $user = $request->user();

        $userId = $user ? (int) $user->id : 0;

        return new self(
            reportId: (int) ($validated['report_id'] ?? $request->input('report_id')),
            userId: $userId,
            rating: (int) ($validated['rating'] ?? 0),
            comment: (string) ($validated['comment'] ?? ''),
            repairId: isset($validated['repair_id']) && $validated['repair_id'] !== '' ? (int) $validated['repair_id'] : null,
            adminResponse: ! empty($validated['admin_response']) ? (string) $validated['admin_response'] : null,
            respondedBy: isset($validated['responded_by']) && $validated['responded_by'] !== '' ? (int) $validated['responded_by'] : null,
            respondedAt: ! empty($validated['responded_at']) ? (string) $validated['responded_at'] : null,
        );
    }

    /**
     * Convert DTO to array for model persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'report_id' => $this->reportId,
            'user_id' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'repair_id' => $this->repairId,
            'admin_response' => $this->adminResponse,
            'responded_by' => $this->respondedBy,
            'responded_at' => $this->respondedAt,
        ], fn ($val) => $val !== null);
    }
}
