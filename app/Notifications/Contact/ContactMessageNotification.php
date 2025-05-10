<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Notifications\Contact;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

final class ContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param array<string,string> $contactData */
    public function __construct(public readonly array $contactData) {}

    /** @return array<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        /** @var string $appName */
        $appName = config(key: 'app.name');

        return (new MailMessage)
            ->subject(subject: trans(key: 'messages.contact.mail.subject', replace: [
                'app'  => $appName,
                'time' => now()->timestamp,
            ]))
            ->line(line: trans(key: 'messages.contact.mail.line1', replace: [
                'app'  => $appName,
                'date' => now()->format(format: 'd/m/Y g:ia'),
            ]))
            ->line(line: new HtmlString(html: trans(key: 'messages.contact.mail.line2', replace: [
                'name' => $this->contactData['name'],
            ])))
            ->line(line: new HtmlString(html: trans(key: 'messages.contact.mail.line3', replace: [
                'email' => $this->contactData['email'],
            ])))
            ->line(line: new HtmlString(html: '<hr />'))
            ->line(line: new HtmlString(html: nl2br(string: $this->contactData['message'])))
            ->replyTo(
                address: $this->contactData['email'],
                name   : $this->contactData['name']
            );
    }

    /**
     * @return array<string,mixed>
     *
     * @phpstan-return array{contactData: array<string,string>}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contactData' => $this->contactData,
        ];
    }
}
