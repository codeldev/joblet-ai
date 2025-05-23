<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPostImageAlreadyGeneratedException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPostImageAlreadyGeneratedException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPostImageAlreadyGeneratedException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.generated'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPostImageAlreadyGeneratedException::class);
});
