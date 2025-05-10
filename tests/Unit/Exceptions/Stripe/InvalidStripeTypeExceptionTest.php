<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripeTypeException;

describe(description: 'InvalidStripeTypeException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripeTypeException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripeTypeException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'payment.webhook.error.payload.type'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripeTypeException::class);
    });
});
