<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\Repair;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_id' => Report::factory(),
            'repair_id' => null,
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'admin_response' => null,
            'responded_by' => null,
            'responded_at' => null,
        ];
    }

    /**
     * Indicate that the feedback has been responded to by an admin.
     */
    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_response' => fake()->paragraph(),
            'responded_by' => User::factory(),
            'responded_at' => now(),
        ]);
    }
}
