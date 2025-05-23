<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentNotArrayException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentNotArrayException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentNotArrayException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.invalid.format'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentNotArrayException::class);
});
