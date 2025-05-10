<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Backups;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class TestBackupServiceImplementation implements BackupServiceInterface
{
    public ?string $error = null;

    public array $methodsCalled = [];

    public function __invoke(): bool
    {
        if ($this->error !== null)
        {
            return false;
        }

        $this->methodsCalled[] = 'createDatabaseDump';
        $result                = $this->createDatabaseDump();
        if (! $result)
        {
            return false;
        }

        $this->methodsCalled[] = 'createBackupFile';
        $result                = $this->createBackupFile();
        if (! $result)
        {
            return false;
        }

        $this->methodsCalled[] = 'storeBackup';

        return $this->storeBackup();
    }

    private function createDatabaseDump(): bool
    {
        return true;
    }

    private function createBackupFile(): bool
    {
        return true;
    }

    private function storeBackup(): bool
    {
        return true;
    }
}
