<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
final class OrderFactory extends Factory
{
    /** @var class-string<Order> */
    protected $model = Order::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $package = collect(value: ProductPackageEnum::cases())
            ->reject(callback: fn ($case): bool => $case === ProductPackageEnum::INTRODUCTION)
            ->random();

        return [
            'user_id'             => User::factory(),
            'package_id'          => $package->value,
            'package_name'        => $package->title(),
            'package_description' => $package->description(),
            'price'               => $package->price(),
            'tokens'              => $package->credits(),
            'free'                => false,
        ];
    }
}
