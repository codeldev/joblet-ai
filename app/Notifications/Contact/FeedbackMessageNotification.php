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

final class FeedbackMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param array<string,string> $feedbackData */
    public function __construct(public readonly array $feedbackData) {}

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
            ->subject(subject: trans(key: 'messages.feedback.mail.subject', replace: [
                'app'  => $appName,
                'time' => now()->timestamp,
            ]))
            ->line(line: trans(key: 'messages.feedback.mail.line1', replace: [
                'app'  => $appName,
                'date' => now()->format(format: 'd/m/Y g:ia'),
            ]))
            ->line(line: new HtmlString(html: trans(key: 'messages.feedback.mail.line2', replace: [
                'name' => notEmpty(value: $this->feedbackData['name'] ?? null)
                    ? $this->feedbackData['name']
                    : trans(key: 'messages.feedback.mail.empty'),
            ])))
            ->line(line: new HtmlString(html: trans(key: 'messages.feedback.mail.line3', replace: [
                'email' => notEmpty(value: $this->feedbackData['email'] ?? null)
                    ? $this->feedbackData['email']
                    : trans(key: 'messages.feedback.mail.empty'),
            ])))
            ->line(line: new HtmlString(html: '<hr />'))
            ->line(line: new HtmlString(html: nl2br(string: $this->feedbackData['message'])))
            ->when(
                value    : notEmpty(value: $this->feedbackData['email']),
                callback : fn ($message) => $message->replyTo(
                    address: $this->feedbackData['email'],
                    name   : $this->feedbackData['name'] ?? 'Guest'
                )
            );
    }

    /**
     * @return array<string,mixed>
     *
     * @phpstan-return array{feedbackData: array<string,string>}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'feedbackData' => $this->feedbackData,
        ];
    }
}
