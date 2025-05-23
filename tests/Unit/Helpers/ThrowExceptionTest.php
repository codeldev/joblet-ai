<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Blog\BlogPromptSystemPromptEmptyException;

it('throws the specified exception class', function (): void
{
    expect(value: fn () => throwException(exceptionClass: BlogPromptSystemPromptEmptyException::class))
        ->toThrow(exception: BlogPromptSystemPromptEmptyException::class);
});

it('throws RuntimeException when class is not an Exception', function (): void
{
    expect(value: fn () => throwException(exceptionClass: stdClass::class))->toThrow(
        exception: RuntimeException::class,
        message  : 'stdClass is not an instance of Exception!'
    );
});
