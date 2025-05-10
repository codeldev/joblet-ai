<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Concerns;

use App\Concerns\HasCommandsTrait;
use Illuminate\Console\Command;

class HasCommandsTest extends Command
{
    use HasCommandsTrait;

    protected $signature = 'test:command {--dry-run : Run in dry-run mode}';

    protected $description = 'Test command for HasCommandsTrait';

    public function handle(): int
    {
        return self::SUCCESS;
    }

    public function getOptionsPublic(): array
    {
        return $this->getOptions();
    }

    public function isDryRunPublic(): bool
    {
        return $this->isDryRun();
    }

    public function isRunningFromSchedulerPublic(): bool
    {
        return $this->isRunningFromScheduler();
    }

    public function isRunningManuallyPublic(): bool
    {
        return $this->isRunningManually();
    }

    public function outputErrorMessagePublic(string $message): void
    {
        $this->outputErrorMessage(message: $message);
    }

    public function outputInfoMessagePublic(string $message): void
    {
        $this->outputInfoMessage(message: $message);
    }

    public function setLaravel($laravel): self
    {
        $this->laravel = $laravel;

        return $this;
    }
}
