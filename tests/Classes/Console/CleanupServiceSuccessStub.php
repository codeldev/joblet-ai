<?php

declare(strict_types=1);

namespace Tests\Classes\Console;

use App\Contracts\Services\Backups\CleanupServiceInterface;

final class CleanupServiceSuccessStub implements CleanupServiceInterface
{
    public ?string $error = null;

    public function __invoke(): bool
    {
        return true;
    }
}
