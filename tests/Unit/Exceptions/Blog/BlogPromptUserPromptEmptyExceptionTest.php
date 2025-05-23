<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptUserPromptEmptyException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptUserPromptEmptyException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptUserPromptEmptyException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.prompt.user.empty'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptUserPromptEmptyException::class);
});
