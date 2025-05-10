<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeUserException;

describe(description: 'InvalidStripeUserException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeUserException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeUserException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.invalid.user'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeUserException::class);
    });
});
