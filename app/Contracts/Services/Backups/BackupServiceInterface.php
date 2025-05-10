<?php

declare(strict_types=1);

namespace App\Contracts\Services\Backups;

interface BackupServiceInterface
{
    public function __invoke(): bool;
}
