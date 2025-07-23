<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\TravelPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $travelPackage = TravelPackage::factory()->create();
        $personCount = $this->faker->numberBetween(1, 4);
        $basePrice = $travelPackage->price;
        $additionalPrice = $this->faker->numberBetween(0, 500000);
        $taxAmount = ($basePrice * $personCount + $additionalPrice) * ($travelPackage->tax_percentage / 100);
        $totalPrice = ($basePrice * $personCount) + $additionalPrice + $taxAmount;
        
        return [
            'user_id' => User::factory(),
            'travel_package_id' => $travelPackage->id,
            'booking_reference' => 'BK-' . strtoupper($this->faker->bothify('????####')) . '-' . date('Ymd'),
            'booking_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'person_count' => $personCount,
            'base_price' => $basePrice,
            'additional_price' => $additionalPrice,
            'tax_amount' => (int) $taxAmount,
            'total_price' => (int) $totalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_requests' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the booking is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }
}