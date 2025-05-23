<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptSystemPromptEmptyException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptSystemPromptEmptyException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptSystemPromptEmptyException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.prompt.system.empty'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptSystemPromptEmptyException::class);
});
