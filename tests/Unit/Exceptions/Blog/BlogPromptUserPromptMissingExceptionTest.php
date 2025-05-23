<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptUserPromptMissingException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptUserPromptMissingException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptUserPromptMissingException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.prompt.user.missing'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptUserPromptMissingException::class);
});
