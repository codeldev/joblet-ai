<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Payment\InvalidPaymentGatewayException;

describe(description: 'InvalidPaymentGatewayException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidPaymentGatewayException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidPaymentGatewayException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.payment.gateway'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidPaymentGatewayException::class);
    });
});
