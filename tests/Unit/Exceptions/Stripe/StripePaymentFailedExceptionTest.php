<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\StripePaymentFailedException;

describe(description: 'StripePaymentFailedException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new StripePaymentFailedException;

        expect(value: $exception)
            ->toBeInstanceOf(class: StripePaymentFailedException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.stripe.charge.failed'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: StripePaymentFailedException::class);
    });
});
