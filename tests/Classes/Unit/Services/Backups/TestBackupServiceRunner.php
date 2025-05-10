<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Backups;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class TestBackupServiceRunner
{
    private BackupServiceInterface $backupService;

    public function __construct(BackupServiceInterface $backupService)
    {
        $this->backupService = $backupService;
    }

    public function runBackup(): bool
    {
        return $this->backupService->__invoke();
    }
}
