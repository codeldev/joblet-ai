<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Backups;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class TestBackupServiceMockWrapper
{
    private BackupServiceInterface $backupService;

    public function __construct(BackupServiceInterface $backupService)
    {
        $this->backupService = $backupService;
        $this->backupService->setSqlName('custom-database.sql');
    }

    public function getBackupService(): BackupServiceInterface
    {
        return $this->backupService;
    }
}
