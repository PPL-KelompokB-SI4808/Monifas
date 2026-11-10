<?php

namespace Database\Factories;

use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityCategory>
 */
class FacilityCategoryFactory extends Factory
{
    protected $model = FacilityCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'FC-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
