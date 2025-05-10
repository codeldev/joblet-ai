<?php

declare(strict_types=1);

use App\Exceptions\Backups\InvalidBackupConfigurationException;

describe(description: 'InvalidBackupConfigurationException', tests: function (): void
{
    it(description: 'can be instantiated and is throwable', closure: function (): void
    {
        $exception = new InvalidBackupConfigurationException();

        expect(value: $exception)
            ->toBeInstanceOf(class: InvalidBackupConfigurationException::class)
            ->and(value: $exception->getMessage())
            ->toBe(expected: trans(key: 'exceptions.backups.config.invalid'))
            ->and(value: fn () => throw $exception)
            ->toThrow(exception: InvalidBackupConfigurationException::class);
    });
});
