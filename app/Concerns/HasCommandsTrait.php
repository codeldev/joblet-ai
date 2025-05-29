<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;

trait HasCommandsTrait
{
    /** @return array<int, array{0: string, 1: string|null, 2: int, 3: string}> */
    protected function getOptions(): array
    {
        return [
            ['dry-run', null, InputOption::VALUE_NONE, 'Run in dry-run mode'],
        ];
    }

    protected function isDryRun(): bool
    {
        return (bool) $this->option(key: 'dry-run');
    }

    protected function isRunningFromScheduler(): bool
    {
        return app()->runningInConsole() && ! defined(constant_name: 'STDIN');
    }

    protected function isRunningManually(): bool
    {
        return ! $this->isRunningFromScheduler();
    }

    protected function outputErrorMessage(string $message): void
    {
        if (app()->isProduction())
        {
            Log::error(message: $message);
        }

        if ($this->isRunningManually())
        {
            $this->error(string: $message);
        }
    }

    protected function outputInfoMessage(string $message): void
    {
        if ($this->isRunningManually())
        {
            $this->info(string: $message);
        }
    }
}
