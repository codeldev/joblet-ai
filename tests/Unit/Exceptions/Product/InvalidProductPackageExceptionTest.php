<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Product\InvalidProductPackageException;

describe(description: 'InvalidProductPackageException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidProductPackageException;

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidProductPackageException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.package.invalid'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidProductPackageException::class);
    });
});
