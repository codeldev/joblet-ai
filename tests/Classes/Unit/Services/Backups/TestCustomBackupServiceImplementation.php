<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Backups;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class TestCustomBackupServiceImplementation implements BackupServiceInterface
{
    public function __invoke(): bool
    {
        return true;
    }
}
