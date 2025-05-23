<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentMissingPostContentException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentMissingPostContentException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentMissingPostContentException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.missing.post.content'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentMissingPostContentException::class);
});
