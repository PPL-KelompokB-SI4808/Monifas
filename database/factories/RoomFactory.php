<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'code' => 'RM-'.strtoupper(fake()->unique()->bothify('##??')),
            'name' => 'Ruang '.fake()->words(2, true),
            'floor' => fake()->numberBetween(1, 4),
            'capacity' => fake()->numberBetween(20, 60),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
