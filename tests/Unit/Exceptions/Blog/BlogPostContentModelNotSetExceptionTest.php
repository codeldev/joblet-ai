<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPostContentModelNotSetException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPostContentModelNotSetException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPostContentModelNotSetException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.model.content.not.set'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPostContentModelNotSetException::class);
});
