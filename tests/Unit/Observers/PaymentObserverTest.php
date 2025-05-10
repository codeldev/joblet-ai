<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Accounting;
use App\Models\Order;
use App\Models\Payment;
use App\Observers\PaymentObserver;

describe(description: 'PaymentObserver', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        config()->set(key: 'settings.invoices.initial', value: 1000);

        Payment::observe(classes: new PaymentObserver);
    });

    test(description: 'sets invoice number when creating a new payment with no existing payments', closure: function (): void
    {
        $user = testUser();

        $order = Order::factory()
            ->for(factory: $user)
            ->create();

        $payment = Payment::factory()
            ->for(factory: $order)
            ->for(factory: $user)
            ->create();

        expect(value: $payment->invoice_number)
            ->toBe(expected: 1001);
    });

    test(description: 'sets invoice number based on the highest existing invoice number', closure: function (): void
    {
        $user = testUser();

        $order = Order::factory()
            ->for(factory: $user)
            ->create();

        $payment = Payment::factory()
            ->for(factory: $order)
            ->for(factory: $user)
            ->create();

        $payment->update(attributes: [
            'invoice_number' => 2000,
        ]);

        Accounting::where(
            column  : 'payment_id',
            operator: '=',
            value   : $payment->id
        )->update([
            'invoice_number' => 2000,
        ]);

        Payment::observe(classes: new PaymentObserver);

        $newPayment = Payment::factory()
            ->for(factory: $order)
            ->for(factory: $user)
            ->create();

        expect(value: $newPayment->invoice_number)
            ->toBe(expected: 2001);
    });

    test(description: 'creates first invoice number when no payments exist', closure: function (): void
    {
        Payment::query()->delete();

        $user = testUser();

        $order = Order::factory()
            ->for(factory: $user)
            ->create();

        $payment = Payment::factory()
            ->for(factory: $order)
            ->for(factory: $user)
            ->create();

        expect(value: $payment->invoice_number)
            ->toBe(expected: 1001);
    });

    test(description: 'handles non-numeric max invoice number by using default value', closure: function (): void
    {
        // Clear existing payments and accounting records
        Payment::query()->delete();
        Accounting::query()->delete();

        // Create an accounting record with a non-numeric invoice number
        Accounting::factory()->create(attributes: [
            'invoice_number' => 'ABC123',
        ]);

        $user = testUser();

        $order = Order::factory()
            ->for(factory: $user)
            ->create();

        $payment = Payment::factory()
            ->for(factory: $order)
            ->for(factory: $user)
            ->create();

        expect(value: $payment->invoice_number)
            ->toBe(expected: 1001);
    });
});
