<?php

declare(strict_types=1);

namespace App\Console\Commands\Backups;

use App\Concerns\HasCommandsTrait;
use App\Contracts\Services\Backups\BackupServiceInterface;
use App\Notifications\Backups\BackupFailedNotification;
use App\Services\Models\UserService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name       : 'backup:create',
    description: 'Create and store a backup.'
)]
final class CreateBackupCommand extends Command
{
    use HasCommandsTrait;

    public function handle(BackupServiceInterface $service): int
    {
        if ($service->__invoke())
        {
            return $this->backupSuccessful();
        }

        /** @var string $message */
        $message = $service->error ?? trans(key: 'backups.unknown.error');

        return $this->backupFailed($message);
    }

    private function backupFailed(string $message): int
    {
        $this->outputErrorMessage(message: $message);
        $this->sendNotification(message: $message);

        return self::FAILURE;
    }

    private function backupSuccessful(): int
    {
        $this->outputInfoMessage(
            message: trans(key: 'backups.created.success')
        );

        return self::SUCCESS;
    }

    private function sendNotification(string $message): void
    {
        UserService::getSupportUser()->notify(
            instance: new BackupFailedNotification(errorMessage: $message)
        );
    }
}
