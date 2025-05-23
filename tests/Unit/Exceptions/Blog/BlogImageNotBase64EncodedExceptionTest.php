<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogImageNotBase64EncodedException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogImageNotBase64EncodedException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogImageNotBase64EncodedException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.base64'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogImageNotBase64EncodedException::class);
});
