<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Usage;
use App\Models\User;
use App\Services\Models\UserService;

beforeEach(function (): void
{
    $this->testUser         = testUser();
    $this->totalCredits     = 22;
    $this->usedCredits      = 5;
    $this->availableCredits = ($this->totalCredits - $this->usedCredits);

    $products = [
        ProductPackageEnum::STANDARD,
        ProductPackageEnum::EXTENDED,
    ];

    collect(value: $products)->each(callback: function (ProductPackageEnum $package): void
    {
        $order = Order::factory()
            ->for(factory: $this->testUser)
            ->create(attributes: [
                'package_id'          => $package->value,
                'package_name'        => $package->title(),
                'package_description' => $package->description(),
                'price'               => $package->price(),
                'tokens'              => $package->credits(),
            ]);

        Payment::factory()
            ->for(factory: $order)
            ->create(attributes: [
                'user_id' => $order->user_id,
                'amount'  => $order->price,
            ]);
    });

    Usage::factory(count: 5)
        ->for(factory: $this->testUser)
        ->create();
});

it('gets the total number of credits for a logged in user', function (): void
{
    $this->actingAs(user: $this->testUser);

    expect(value: UserService::getTotalCredits())
        ->toBeInt()
        ->toEqual(expected: $this->totalCredits);
});

it('gets the total number of used credits for a logged in user', function (): void
{
    $this->actingAs(user: $this->testUser);

    expect(value: UserService::getUsedCredits())
        ->toBeInt()
        ->toEqual(expected: $this->usedCredits);
});

it('gets the total number of available credits for a logged in user', function (): void
{
    $this->actingAs(user: $this->testUser);

    expect(value: UserService::getRemainingCredits())
        ->toBeInt()
        ->toEqual(expected: $this->availableCredits);
});

it('returns 0 as the total number of credits for a guest', function (): void
{
    expect(value: UserService::getTotalCredits())
        ->toBeInt()
        ->toEqual(expected: 0);
});

it('returns 0 as the total number of used credits for a guest', function (): void
{
    expect(value: UserService::getUsedCredits())
        ->toBeInt()
        ->toEqual(expected: 0);
});

it('returns 0 as the total number of available credits for a guest', function (): void
{
    expect(value: UserService::getRemainingCredits())
        ->toBeInt()
        ->toEqual(expected: 0);
});

it('returns a valid User model based on support email', function (): void
{
    $email = config(key: 'settings.contact');

    testUser(params: ['email' => $email]);

    expect(value: $user = UserService::getSupportUser())
        ->toBeInstanceOf(class: User::class)
        ->and(value: $user->email)
        ->toBe(expected: $email);
});
