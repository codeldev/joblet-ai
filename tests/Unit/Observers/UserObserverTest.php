<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use App\Models\User;
use App\Observers\UserObserver;

describe(description: 'UserObserver', tests: function (): void
{
    test(description: 'creates intro order on user creation', closure: function (): void
    {
        User::observe(classes: new UserObserver);

        $user    = testUser();
        $order   = $user->orders()->first();
        $package = ProductPackageEnum::INTRODUCTION;

        expect(value: $order)
            ->not->toBeNull()
            ->and(value: $order->package_id)
            ->toBe(expected: $package->value)
            ->and(value: $order->package_name)
            ->toBe(expected: $package->title())
            ->and(value: $order->package_description)
            ->toBe(expected: $package->description())
            ->and(value: $order->price)
            ->toBe(expected: $package->price())
            ->and(value: $order->tokens)
            ->toBe(expected: $package->credits())
            ->and(value: $order->free)
            ->toBeTrue();
    });
});
