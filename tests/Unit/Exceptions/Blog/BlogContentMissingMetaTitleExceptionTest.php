<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentMissingMetaTitleException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentMissingMetaTitleException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentMissingMetaTitleException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.missing.meta.title'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentMissingMetaTitleException::class);
});
