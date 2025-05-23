<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogIdeaAlreadyProcessedException;

it('can be instantiated and is throwable', function (): void
{
    $exception = new BlogIdeaAlreadyProcessedException();

    expect(value: $exception)
        ->toBeInstanceOf(class: BlogIdeaAlreadyProcessedException::class)
        ->and(value: $exception->getMessage())
        ->toBe(expected: trans(key: 'exception.blog.queue.idea.processed'))
        ->and(value: fn () => throw $exception)
        ->toThrow(exception: BlogIdeaAlreadyProcessedException::class);
});
