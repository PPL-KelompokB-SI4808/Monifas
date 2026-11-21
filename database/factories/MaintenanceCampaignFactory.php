<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Models\Building;
use App\Models\FacilityCategory;
use App\Models\MaintenanceCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceCampaign>
 */
class MaintenanceCampaignFactory extends Factory
{
    protected $model = MaintenanceCampaign::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'building_id' => Building::factory(),
            'facility_category_id' => FacilityCategory::factory(),
            'title' => fake()->sentence(4),
            'objective' => fake()->paragraph(),
            'content' => fake()->paragraphs(2, true),
            'banner_path' => null,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'status' => CampaignStatus::Scheduled,
        ];
    }

    /**
     * Indicate that the campaign is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(16)->toDateString(),
            'status' => CampaignStatus::Scheduled,
        ]);
    }

    /**
     * Indicate that the campaign is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'status' => CampaignStatus::Active,
        ]);
    }

    /**
     * Indicate that the campaign has ended.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(15)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => CampaignStatus::Ended,
        ]);
    }

    /**
     * Indicate that the campaign is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::Cancelled,
        ]);
    }
}
