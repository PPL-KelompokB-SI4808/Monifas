<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    protected $model = Building::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'area_zone_id' => null,
            'code' => 'BLD-'.fake()->unique()->numerify('####'),
            'name' => 'Gedung '.fake()->words(2, true),
            'description' => fake()->sentence(),
            'total_floors' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
