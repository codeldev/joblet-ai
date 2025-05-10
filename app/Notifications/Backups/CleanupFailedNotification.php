<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Notifications\Backups;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class CleanupFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $errorMessage) {}

    /** @return array<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        /** @var string $app */
        $app = config(key: 'app.name');

        return (new MailMessage)
            ->subject(subject: trans(key: 'backups.email.clean.failed.subject', replace: ['app' => $app]))
            ->line(line: trans(key: 'backups.email.clean.failed.line', replace: ['app' => $app]))
            ->line(line: $this->errorMessage);
    }

    /** @return array<string,string> */
    public function toArray(object $notifiable): array
    {
        return [
            'error_message' => $this->errorMessage,
        ];
    }
}
