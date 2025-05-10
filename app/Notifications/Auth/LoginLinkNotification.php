<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

final class LoginLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /** @return array<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        /** @var string $app */
        $app  = config(key: 'app.name');
        $link = $this->loginLinkUrl(notifiable: $notifiable);

        return (new MailMessage)
            ->subject(subject: trans(key: 'auth.sign.in.email.subject', replace: ['app' => $app]))
            ->greeting(greeting: trans(key: 'email.greeting.simple'))
            ->line(line: trans(key: 'auth.sign.in.email.line1', replace: ['app' => $app]))
            ->action(text: trans(key: 'auth.sign.in.email.button'), url: $link)
            ->line(line: trans(key: 'auth.sign.in.email.line2'))
            ->line(line: trans(key: 'auth.sign.in.email.line3'));
    }

    private function loginLinkUrl(User $notifiable): string
    {
        return URL::temporarySignedRoute(
            name      : 'magic',
            expiration: now()->addMinutes(value: 15),
            parameters: [
                'id'   => $notifiable->id,
                'hash' => sha1(string: $notifiable->email),
            ]
        );
    }
}
