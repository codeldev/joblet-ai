<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogIdeaNotFoundDuringQueuedJobException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogIdeaNotFoundDuringQueuedJobException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogIdeaNotFoundDuringQueuedJobException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.queue.idea.missing'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogIdeaNotFoundDuringQueuedJobException::class);
});
