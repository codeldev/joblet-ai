<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\ProviderApiKeyNotConfiguredException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new ProviderApiKeyNotConfiguredException();

    expect(value: $exception)
        ->toBeInstanceOf(class: ProviderApiKeyNotConfiguredException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.ai.api.missing.key'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: ProviderApiKeyNotConfiguredException::class);
});
