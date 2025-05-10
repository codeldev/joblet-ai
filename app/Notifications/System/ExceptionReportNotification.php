<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Notifications\System;

use App\Services\Notifications\ExceptionNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

final class ExceptionReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param array<string,mixed> $exceptionData */
    public function __construct(public readonly array $exceptionData) {}

    /** @return array<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $service   = new ExceptionNotificationService;
        $dataset   = $service->buildReportData($this->exceptionData);
        $traceData = $this->exceptionData['trace'] ?? null;

        /** @var string $appName */
        $appName = config(key: 'app.name', default: 'App');

        $mailMessage = (new MailMessage)
            ->subject(subject: trans(key: 'exception.email.subject', replace: ['app' => $appName]))
            ->line(line: trans(key: 'exception.email.intro'))
            ->line(line: new HtmlString(html: '<hr />'))
            ->line(line: trans(key: 'exception.email.url', replace: $dataset))
            ->line(line: trans(key: 'exception.email.ip', replace: $dataset))
            ->line(line: trans(key: 'exception.email.user', replace: $dataset))
            ->line(line: new HtmlString(html: '<hr />'))
            ->line(line: trans(key: 'exception.email.error', replace: $dataset))
            ->line(line: trans(key: 'exception.email.file', replace: $dataset))
            ->line(line: trans(key: 'exception.email.line', replace: $dataset));

        if (is_array($traceData) && count($traceData) > 0)
        {
            $mailMessage->line(line: new HtmlString(html: '<hr />'));
            $mailMessage->line(line: trans(key: 'exception.email.trace.start'));

            foreach ($service->buildTraceData(traceData: $traceData) as $trace)
            {
                if (notEmpty(value: $trace))
                {
                    $mailMessage->line(line: $trace);
                }
            }

            $mailMessage->line(line: new HtmlString(html: '<hr />'));
            $mailMessage->line(line: trans(key: 'exception.email.trace.end'));
        }
        else
        {
            $mailMessage->line(line: trans(key: 'exception.email.trace.none'));
        }

        return $mailMessage;
    }

    /**
     * @return array<string,mixed>
     *
     * @phpstan-return array{exceptionData: array<string,mixed>}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'exceptionData' => $this->exceptionData,
        ];
    }
}
