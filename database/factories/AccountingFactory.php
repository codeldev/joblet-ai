<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductPackageEnum;
use App\Models\Accounting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Accounting> */
final class AccountingFactory extends Factory
{
    /** @var class-string<Accounting> */
    protected $model = Accounting::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $package = collect(value: ProductPackageEnum::cases())
            ->reject(callback: fn ($case): bool => $case === ProductPackageEnum::INTRODUCTION)
            ->random();

        return [
            'user_name'           => fake()->name(),
            'user_email'          => str(string: fake()->name())->slug()->toString() . '@gmail.com',
            'order_id'            => Str::uuid(),
            'package_id'          => $package->value,
            'package_name'        => $package->title(),
            'package_description' => $package->description(),
            'price'               => $package->price(),
            'tokens'              => $package->credits(),
            'payment_id'          => Str::uuid(),
            'amount'              => $package->price(),
            'gateway'             => 'stripe',
            'card_type'           => fake()->randomElement(array: ['visa', 'mastercard']),
            'card_last4'          => fake()->numberBetween(int1: 1000, int2: 9999),
            'event_id'            => Str::uuid(),
            'intent_id'           => Str::uuid(),
            'charge_id'           => Str::uuid(),
            'transaction_id'      => Str::uuid(),
            'receipt_url'         => fake()->url(),
            'payment_token'       => Str::uuid(),
        ];
    }
}
