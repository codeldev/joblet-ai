<?php

declare(strict_types=1);

namespace App\Contracts\Services\Backups;

interface CleanupServiceInterface
{
    public function __invoke(): bool;
}
