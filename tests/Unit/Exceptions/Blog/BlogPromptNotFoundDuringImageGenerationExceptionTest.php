<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptNotFoundDuringImageGenerationException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogPromptNotFoundDuringImageGenerationException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogPromptNotFoundDuringImageGenerationException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.image.prompt.missing'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogPromptNotFoundDuringImageGenerationException::class);
});
