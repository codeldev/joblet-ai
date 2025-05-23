<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentMissingPostSummaryException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentMissingPostSummaryException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentMissingPostSummaryException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.missing.post.summary'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentMissingPostSummaryException::class);
});
