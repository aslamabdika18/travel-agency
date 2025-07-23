<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::factory()->create();
        
        return [
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . $booking->id . '-' . time(),
            'transaction_id' => 'TXN-' . $booking->id . '-' . time(),
            'gateway_transaction_id' => null,
            'total_price' => $booking->total_price,
            'payment_status' => 'unpaid',
            'gateway_status' => null,
            'payment_date' => now(),
            'gateway_response' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the payment is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'gateway_status' => 'settlement',
            'gateway_transaction_id' => 'BOOKING-' . $attributes['booking_id'] . '-' . time(),
            'payment_date' => now(),
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
            'gateway_status' => 'deny',
            'gateway_transaction_id' => 'BOOKING-' . $attributes['booking_id'] . '-' . time(),
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'unpaid',
            'gateway_status' => 'pending',
            'gateway_transaction_id' => 'BOOKING-' . $attributes['booking_id'] . '-' . time(),
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'refunded',
            'gateway_status' => 'refund',
            'gateway_transaction_id' => 'BOOKING-' . $attributes['booking_id'] . '-' . time(),
        ]);
    }
}