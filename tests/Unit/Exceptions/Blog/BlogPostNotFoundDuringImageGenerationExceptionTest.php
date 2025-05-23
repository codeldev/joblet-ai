<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPostNotFoundDuringImageGenerationException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPostNotFoundDuringImageGenerationException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPostNotFoundDuringImageGenerationException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.post.missing'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPostNotFoundDuringImageGenerationException::class);
});
