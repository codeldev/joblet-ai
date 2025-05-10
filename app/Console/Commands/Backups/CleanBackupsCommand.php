<?php

declare(strict_types=1);

namespace App\Console\Commands\Backups;

use App\Concerns\HasCommandsTrait;
use App\Contracts\Services\Backups\CleanupServiceInterface;
use App\Notifications\Backups\CleanupFailedNotification;
use App\Services\Models\UserService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name        : 'backup:clean',
    description : 'Remove all backups older than specified number of days in config.'
)]
final class CleanBackupsCommand extends Command
{
    use HasCommandsTrait;

    public function handle(CleanupServiceInterface $service): int
    {
        if ($service->__invoke())
        {
            return $this->cleanupSuccess();
        }

        /** @var string $message */
        $message = $service->error ?? trans(key: 'backups.unknown.error');

        return $this->cleanupFailed($message);
    }

    private function cleanupSuccess(): int
    {
        $this->outputInfoMessage(
            message: trans(key: 'backups.cleanup.success')
        );

        return self::SUCCESS;
    }

    private function cleanupFailed(string $message): int
    {
        $this->outputInfoMessage(message: $message);
        $this->sendNotification(message: $message);

        return self::FAILURE;
    }

    private function sendNotification(string $message): void
    {
        UserService::getSupportUser()->notify(
            instance: new CleanupFailedNotification(errorMessage: $message)
        );
    }
}
