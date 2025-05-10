<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\Uploads\UploadedResumeEmptyException;

describe(description: 'UploadedResumeEmptyException', tests: function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new UploadedResumeEmptyException;

        expect(value: $exception)
            ->toBeInstanceOf(class: UploadedResumeEmptyException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exception.upload.resume.empty'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: UploadedResumeEmptyException::class);
    });
});
