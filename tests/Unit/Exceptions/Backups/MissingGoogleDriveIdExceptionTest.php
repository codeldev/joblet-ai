<?php

declare(strict_types=1);

use App\Exceptions\Backups\MissingGoogleDriveIdException;

describe(description: 'MissingGoogleDriveIdException', tests: function (): void
{
    it(description: 'can be instantiated and is throwable', closure: function (): void
    {
        $exception = new MissingGoogleDriveIdException();

        expect(value: $exception)
            ->toBeInstanceOf(class: MissingGoogleDriveIdException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exceptions.backups.google.id'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: MissingGoogleDriveIdException::class);
    });
});
