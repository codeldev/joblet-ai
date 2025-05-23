<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentMissingImagePromptException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentMissingImagePromptException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentMissingImagePromptException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.missing.image.prompt'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentMissingImagePromptException::class);
});
