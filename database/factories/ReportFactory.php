<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'RPT-'.date('Ym').'-'.fake()->unique()->numerify('####'),
            'reporter_id' => User::factory(),
            'location_id' => Location::factory(),
            'building_id' => Building::factory(),
            'room_id' => null,
            'facility_id' => Facility::factory(),
            'damage_category_id' => DamageCategory::factory(),
            'priority_id' => fn () => ReportPriority::inRandomOrder()->first()?->id ?? ReportPriority::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => ReportStatus::Pending,
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
            'completed_at' => null,
        ];
    }

    /**
     * State for verified report.
     */
    public function verified(?User $supervisor = null): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Verified,
            'verified_by' => $supervisor?->id ?? User::factory(),
            'verified_at' => now(),
        ]);
    }

    /**
     * State for assigned report.
     */
    public function assigned(?User $supervisor = null): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Assigned,
            'verified_by' => $supervisor?->id ?? User::factory(),
            'verified_at' => now()->subHours(2),
        ]);
    }

    /**
     * State for in progress report.
     */
    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::InProgress,
            'verified_by' => User::factory(),
            'verified_at' => now()->subDay(),
        ]);
    }

    /**
     * State for completed report.
     */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Completed,
            'verified_by' => User::factory(),
            'verified_at' => now()->subDays(2),
            'completed_at' => now(),
        ]);
    }

    /**
     * State for rejected report.
     */
    public function rejected(?string $reason = null, ?User $supervisor = null): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Rejected,
            'verified_by' => $supervisor?->id ?? User::factory(),
            'verified_at' => now(),
            'rejection_reason' => $reason ?? 'Laporan tidak memenuhi kriteria kerusakan fasilitas atau data lokasi tidak akurat.',
        ]);
    }
}
