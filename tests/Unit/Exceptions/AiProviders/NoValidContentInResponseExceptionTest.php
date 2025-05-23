<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\NoValidContentInResponseException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new NoValidContentInResponseException();

    expect(value: $exception)
        ->toBeInstanceOf(class: NoValidContentInResponseException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.ai.invalid.content'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: NoValidContentInResponseException::class);
});
