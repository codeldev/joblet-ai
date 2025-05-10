<?php

declare(strict_types=1);

namespace Tests\Classes\Console;

use App\Contracts\Services\Backups\CleanupServiceInterface;

final class CleanupServiceErrorStub implements CleanupServiceInterface
{
    public ?string $error = 'Custom cleanup error';

    public function __invoke(): bool
    {
        return false;
    }
}
