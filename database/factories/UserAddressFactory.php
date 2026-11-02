<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAddress>
 */
class UserAddressFactory extends Factory
{
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Apartemen', 'Gudang']),
            'building_or_unit' => 'Gedung '.fake()->randomElement(['A', 'B', 'C']).' Lantai '.fake()->numberBetween(1, 5),
            'address_detail' => fake()->streetAddress().', '.fake()->city(),
            'contact_phone' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'is_primary' => false,
        ];
    }

    /**
     * Indicate that the address is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
