<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeTokenException;

describe(description: 'InvalidStripeTokenException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeTokenException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeTokenException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.invalid.token'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeTokenException::class);
    });
});
