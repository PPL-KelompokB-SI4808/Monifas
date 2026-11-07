<?php

namespace Database\Factories;

use App\Models\DamageCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DamageCategory>
 */
class DamageCategoryFactory extends Factory
{
    protected $model = DamageCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'DC-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(2, true),
            'severity_hint' => fake()->randomElement(['Memerlukan Penanganan Segera', 'Penanganan Standar', 'Dapat Ditunda']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
