<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\OrderPayment;
use App\Models\Payment;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();

    $this->actingAs(user: $this->testUser);
});

it('can render the order payment component', function (): void
{
    Livewire::test(name: OrderPayment::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.credits.order.button'));
});

it('shows payment cancelled callout', function (): void
{
    Session::put('payment-cancelled', true);

    Livewire::test(name: OrderPayment::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'payment.state.cancelled.text'))
        ->call(method: 'reloadPayment')
        ->assertSet(name: 'paymentSuccess', value: false);
});

it('shows payment error callout', function (): void
{
    Session::put('payment-error', 'A payment error occurred.');

    Livewire::test(name: OrderPayment::class)
        ->assertOk()
        ->assertSee(values: 'A payment error occurred.');
});

it('shows payment processing callout', function (): void
{
    Session::put('payment-token', Str::uuid()->toString());

    Livewire::test(name: OrderPayment::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'payment.state.processing.text'));
});

it('shows payment verified callout', function (): void
{
    $token = Str::uuid()->toString();

    Session::put('payment-token', $token);

    $component = Livewire::test(name: OrderPayment::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'payment.state.processing.text'));

    Payment::factory()
        ->for(factory: $this->testUser)
        ->create(attributes: [
            'payment_token' => $token,
        ]);

    $component->call(method: 'validatePayment')
        ->assertDispatched(event: 'reload-account')
        ->assertSee(values: trans(key: 'payment.state.verified.text'));
});
