<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new OpenAiApiKeyNotConfiguredException();

    expect(value: $exception)
        ->toBeInstanceOf(class: OpenAiApiKeyNotConfiguredException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.ai.api.openai'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: OpenAiApiKeyNotConfiguredException::class);
});
