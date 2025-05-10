<?php

declare(strict_types=1);

use App\Exceptions\Backups\MissingBackupConfigurationException;

describe(description: 'MissingBackupConfigurationException', tests: function (): void
{
    it(description: 'can be instantiated and is throwable', closure: function (): void
    {
        $exception = new MissingBackupConfigurationException();

        expect(value: $exception)
            ->toBeInstanceOf(class: MissingBackupConfigurationException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exceptions.backups.config.missing'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: MissingBackupConfigurationException::class);
    });
});
