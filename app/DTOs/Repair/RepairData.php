<?php

namespace App\DTOs\Repair;

use Illuminate\Http\Request;

class RepairData
{
    public function __construct(
        public readonly int $assignment_id,
        public readonly ?int $report_id,
        public readonly int $facility_id,
        public readonly int $officer_id,
        public readonly string $started_at,
        public readonly ?string $ended_at,
        public readonly string $technical_action,
        public readonly ?string $repair_result,
        public readonly string $resulting_facility_condition,
        public readonly string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            assignment_id: (int) $data['assignment_id'],
            report_id: isset($data['report_id'])
                ? (int) $data['report_id']
                : null,
            facility_id: (int) $data['facility_id'],
            officer_id: (int) $data['officer_id'],
            started_at: $data['started_at'],
            ended_at: $data['ended_at'] ?? null,
            technical_action: $data['technical_action'],
            repair_result: $data['repair_result'] ?? null,
            resulting_facility_condition:
                $data['resulting_facility_condition'] ?? 'good',
            status: $data['status'] ?? 'draft',
        );
    }

    public function toArray(): array
    {
        return [
            'assignment_id' => $this->assignment_id,
            'report_id' => $this->report_id,
            'facility_id' => $this->facility_id,
            'officer_id' => $this->officer_id,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'technical_action' => $this->technical_action,
            'repair_result' => $this->repair_result,
            'resulting_facility_condition' =>
                $this->resulting_facility_condition,
            'status' => $this->status,
        ];
    }
}