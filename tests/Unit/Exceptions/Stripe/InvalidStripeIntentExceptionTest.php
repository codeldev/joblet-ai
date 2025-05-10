<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeIntentException;

describe(description: 'InvalidStripeIntentException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeIntentException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeIntentException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.invalid.intent'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeIntentException::class);
    });
});
