<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Payment\InvalidPaymentUrlException;

describe(description: 'InvalidPaymentUrlException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidPaymentUrlException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidPaymentUrlException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.payment.url'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidPaymentUrlException::class);
    });
});
