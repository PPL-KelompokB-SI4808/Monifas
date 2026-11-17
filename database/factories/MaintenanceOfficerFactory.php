<?php

namespace Database\Factories;

use App\Enums\OfficerAvailability;
use App\Models\MaintenanceOfficer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceOfficer>
 */
class MaintenanceOfficerFactory extends Factory
{
    protected $model = MaintenanceOfficer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_code' => 'MNT-'.fake()->unique()->numerify('######'),
            'specialization_notes' => fake()->sentence(),
            'availability_status' => OfficerAvailability::Available,
            'max_active_tasks' => 5,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the officer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the officer is busy.
     */
    public function busy(): static
    {
        return $this->state(fn () => [
            'availability_status' => OfficerAvailability::Busy,
        ]);
    }
}
