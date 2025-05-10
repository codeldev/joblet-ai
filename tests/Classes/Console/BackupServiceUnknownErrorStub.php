<?php

declare(strict_types=1);

namespace Tests\Classes\Console;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class BackupServiceUnknownErrorStub implements BackupServiceInterface
{
    public ?string $error = null;

    public function __invoke(): bool
    {
        return false;
    }
}
