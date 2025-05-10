<?php

declare(strict_types=1);

use App\Exceptions\Backups\MissingGoogleDriveSecretException;

describe(description: 'MissingGoogleDriveSecretException', tests: function (): void
{
    it(description: 'can be instantiated and is throwable', closure: function (): void
    {
        $exception = new MissingGoogleDriveSecretException();

        expect(value: $exception)
            ->toBeInstanceOf(class: MissingGoogleDriveSecretException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exceptions.backups.google.secret'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: MissingGoogleDriveSecretException::class);
    });
});
