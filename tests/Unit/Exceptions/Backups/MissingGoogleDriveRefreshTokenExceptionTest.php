<?php

declare(strict_types=1);

use App\Exceptions\Backups\MissingGoogleDriveRefreshTokenException;

describe(description: 'MissingGoogleDriveRefreshTokenException', tests: function (): void
{
    it(description: 'can be instantiated and is throwable', closure: function (): void
    {
        $exception = new MissingGoogleDriveRefreshTokenException();

        expect(value: $exception)
            ->toBeInstanceOf(class: MissingGoogleDriveRefreshTokenException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exceptions.backups.google.token'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: MissingGoogleDriveRefreshTokenException::class);
    });
});
