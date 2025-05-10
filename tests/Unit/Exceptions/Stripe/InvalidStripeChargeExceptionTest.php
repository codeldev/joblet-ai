<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeChargeException;

describe(description: 'InvalidStripeChargeException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeChargeException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeChargeException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.invalid.charge'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeChargeException::class);
    });
});
