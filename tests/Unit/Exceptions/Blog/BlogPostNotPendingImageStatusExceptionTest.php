<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPostNotPendingImageStatusException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPostNotPendingImageStatusException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPostNotPendingImageStatusException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.invalid.status'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPostNotPendingImageStatusException::class);
});
