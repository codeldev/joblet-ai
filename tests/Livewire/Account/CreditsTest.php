<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use App\Livewire\Account\Credits;
use Livewire\Livewire;

it('can render the credits component', function (): void
{
    $package = ProductPackageEnum::INTRODUCTION;

    Livewire::actingAs(user: testUser())
        ->test(name: Credits::class)
        ->assertSee(values: trans(key: 'account.credits.order.button'))
        ->assertSee(values: trans(key: 'account.credits.available', replace: [
            'available' => $package->credits(),
        ]));
});

it('sends for payment when a credit pack is selected', function (): void
{
    $package = ProductPackageEnum::PACKAGE_A;

    Livewire::actingAs(user: testUser())
        ->test(name: Credits::class)
        ->set('packageId', $package->value)
        ->assertDispatched(event: 'modal-close', name: 'order-credits')
        ->assertDispatched('send-for-payment', $package->value);
});

it('redirects to stripe when sending for payment', function (): void
{
    $package = ProductPackageEnum::PACKAGE_A;
    $route   = route(name: 'payment.request', parameters: [
        'gateway' => 'stripe',
        'package' => $package->value,
    ]);

    Livewire::actingAs(user: testUser())
        ->test(name: Credits::class)
        ->call('sendForPayment', $package->value)
        ->assertRedirect(uri: $route);
});
