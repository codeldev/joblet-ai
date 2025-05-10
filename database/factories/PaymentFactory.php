<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Payment> */
final class PaymentFactory extends Factory
{
    /** @var class-string<Payment> */
    protected $model = Payment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $order = Order::factory()->create();

        return [
            'order_id'       => $order->id,
            'user_id'        => $order->user_id,
            'amount'         => $order->price,
            'gateway'        => 'stripe',
            'card_type'      => fake()->randomElement(array: ['visa', 'mastercard']),
            'card_last4'     => fake()->numberBetween(int1: 1000, int2: 9999),
            'event_id'       => Str::uuid(),
            'intent_id'      => Str::uuid(),
            'charge_id'      => Str::uuid(),
            'transaction_id' => Str::uuid(),
            'receipt_url'    => fake()->url(),
            'payment_token'  => Str::uuid(),
        ];
    }
}
