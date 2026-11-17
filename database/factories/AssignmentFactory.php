<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\MaintenanceOfficer;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_id' => Report::factory()->assigned(),
            'maintenance_schedule_id' => null,
            'officer_id' => MaintenanceOfficer::factory(),
            'assigned_by' => User::factory(),
            'status' => AssignmentStatus::Assigned,
            'sla_response_due_at' => now()->addHours(4),
            'sla_resolution_due_at' => now()->addDay(),
            'accepted_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'rejection_or_cancel_reason' => null,
            'instruction_notes' => null,
        ];
    }

    /**
     * Indicate that the assignment is active.
     */
    public function assigned(): static
    {
        return $this->state(fn () => [
            'status' => AssignmentStatus::Assigned,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => AssignmentStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate that the assignment is cancelled.
     */
    public function cancelled(?string $reason = null): static
    {
        return $this->state(fn () => [
            'status' => AssignmentStatus::Cancelled,
            'rejection_or_cancel_reason' => $reason ?? 'Penugasan dibatalkan.',
        ]);
    }
}
