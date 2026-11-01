<?php

namespace Database\Factories;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'title' => fake()->sentence(5),
            'content' => fake()->paragraphs(2, true),
            'category' => fake()->randomElement(['Pemeliharaan Rutin', 'Keadaan Darurat', 'Informasi Umum']),
            'target_role' => null,
            'target_building_id' => null,
            'publish_at' => now()->subHour(),
            'expires_at' => now()->addDays(7),
            'status' => AnnouncementStatus::Published,
        ];
    }

    /**
     * Indicate that the announcement is in draft state.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AnnouncementStatus::Draft,
        ]);
    }

    /**
     * Indicate that the announcement is scheduled for the future.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_at' => now()->addDays(2),
            'expires_at' => now()->addDays(9),
            'status' => AnnouncementStatus::Scheduled,
        ]);
    }

    /**
     * Indicate that the announcement is actively published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_at' => now()->subDay(),
            'expires_at' => now()->addDays(5),
            'status' => AnnouncementStatus::Published,
        ]);
    }

    /**
     * Indicate that the announcement has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_at' => now()->subDays(10),
            'expires_at' => now()->subDay(),
            'status' => AnnouncementStatus::Archived,
        ]);
    }

    /**
     * Indicate that the announcement is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AnnouncementStatus::Archived,
        ]);
    }

    /**
     * Indicate that the announcement is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AnnouncementStatus::Cancelled,
        ]);
    }

    /**
     * Target a specific user role.
     */
    public function forRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'target_role' => $role,
        ]);
    }

    /**
     * Target a specific building.
     */
    public function forBuilding(int $buildingId): static
    {
        return $this->state(fn (array $attributes) => [
            'target_building_id' => $buildingId,
        ]);
    }
}

