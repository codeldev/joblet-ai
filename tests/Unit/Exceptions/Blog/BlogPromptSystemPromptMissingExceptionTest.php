<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptSystemPromptMissingException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptSystemPromptMissingException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptSystemPromptMissingException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.prompt.system.missing'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptSystemPromptMissingException::class);
});
