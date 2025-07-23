<?php

namespace Database\Factories;

use App\Models\TravelPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelPackage>
 */
class TravelPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TravelPackage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(1000000, 10000000), // 1-10 juta
            'base_person_count' => $this->faker->numberBetween(1, 2),
            'additional_person_price' => $this->faker->numberBetween(500000, 2000000),
            'capacity' => $this->faker->numberBetween(10, 50),
            'duration' => $this->faker->randomElement(['3 Days 2 Nights', '5 Days 4 Nights', '7 Days 6 Nights']),
            'tax_percentage' => $this->faker->randomFloat(2, 10, 15), // 10-15%
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the travel package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the travel package is expensive.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->numberBetween(15000000, 50000000), // 15-50 juta
        ]);
    }
}