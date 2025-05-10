<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Stripe\InvalidStripePayloadException;

describe(description: 'InvalidStripePayloadException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStripePayloadException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidStripePayloadException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'payment.webhook.error.payload.invalid'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidStripePayloadException::class);
    });
});
