<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'building_id' => Building::factory(),
            'room_id' => null,
            'facility_category_id' => FacilityCategory::factory(),
            'code' => 'FAC-'.fake()->unique()->numerify('####'),
            'name' => 'Fasilitas '.fake()->words(2, true),
            'specifications' => fake()->sentence(),
            'condition' => 'good',
            'is_active' => true,
        ];
    }
}
