<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptImagePromptMissingException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptImagePromptMissingException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptImagePromptMissingException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.prompt.empty'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptImagePromptMissingException::class);
});
