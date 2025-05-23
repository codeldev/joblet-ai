<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogContentMissingMetaDescriptionException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogContentMissingMetaDescriptionException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogContentMissingMetaDescriptionException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.content.missing.meta.description'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogContentMissingMetaDescriptionException::class);
});
