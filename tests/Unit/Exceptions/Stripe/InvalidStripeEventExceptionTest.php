<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeEventException;

describe(description: 'InvalidStripeEventException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeEventException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeEventException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.invalid.event'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeEventException::class);
    });
});
