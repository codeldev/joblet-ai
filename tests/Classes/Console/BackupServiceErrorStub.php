<?php

declare(strict_types=1);

namespace Tests\Classes\Console;

use App\Contracts\Services\Backups\BackupServiceInterface;

final class BackupServiceErrorStub implements BackupServiceInterface
{
    public ?string $error = 'Custom error message';

    public function __invoke(): bool
    {
        return false;
    }
}
