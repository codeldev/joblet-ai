<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\AnthropicApiKeyNotConfiguredException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new AnthropicApiKeyNotConfiguredException();

    expect(value: $exception)
        ->toBeInstanceOf(class: AnthropicApiKeyNotConfiguredException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.ai.api.anthropic'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: AnthropicApiKeyNotConfiguredException::class);
});
