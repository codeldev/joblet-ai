<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Notifications\System\ExceptionReportNotification;
use Illuminate\Notifications\Messages\MailMessage;

describe(description: 'ExceptionReportNotification', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        $this->exceptionData = [
            'message' => fake()->sentence(),
            'file'    => fake()->filePath(),
            'line'    => fake()->numberBetween(int1: 100, int2: 999),
            'trace'   => [
                [
                    'class'    => 'App\\Services\\' . fake()->word() . 'Service',
                    'function' => fake()->word(),
                    'file'     => fake()->filePath(),
                    'line'     => fake()->numberBetween(int1: 100, int2: 999),
                ],
                [
                    'class'    => 'App\\Controllers\\' . fake()->word() . 'Controller',
                    'function' => fake()->word(),
                    'file'     => fake()->filePath(),
                    'line'     => fake()->numberBetween(int1: 100, int2: 999),
                ],
            ],
            'url'  => fake()->url(),
            'body' => ['param' => fake()->word()],
            'ip'   => fake()->ipv4(),
            'user' => fake()->name(),
        ];

        $this->notification = new ExceptionReportNotification(
            exceptionData: $this->exceptionData
        );
    });

    test(description: 'via returns mail channel', closure: function (): void
    {
        expect(value: $this->notification->via(notifiable: testUser()))
            ->toBe(expected: ['mail']);
    });

    test(description: 'toMail returns correct MailMessage', closure: function (): void
    {
        expect(value: $this->notification->toMail(notifiable: testUser()))
            ->toBeInstanceOf(class: MailMessage::class);
    });

    test(description: 'toMail contains correct content', closure: function (): void
    {
        $mailMessage  = $this->notification->toMail(notifiable: testUser());
        $collection   = collect(value: $mailMessage->introLines);
        $filterResult = function (string $contains) use ($collection): int
        {
            $needle = is_string($this->exceptionData[$contains])
                ? $this->exceptionData[$contains]
                : (string) $this->exceptionData[$contains];

            return $collection->filter(
                callback: fn ($line) => is_string(value: $line) && str_contains($line, $needle)
            )->count();
        };

        expect(value: $mailMessage->subject)
            ->toContain(needle: config(key: 'app.name'))
            ->and(value: $mailMessage->introLines)
            ->toBeArray()
            ->and(value: $filterResult(contains: 'url'))
            ->toBeGreaterThan(expected: 0)
            ->and(value: $filterResult(contains: 'ip'))
            ->toBeGreaterThan(expected: 0)
            ->and(value: $filterResult(contains: 'user'))
            ->toBeGreaterThan(expected: 0)
            ->and(value: $filterResult(contains: 'message'))
            ->toBeGreaterThan(expected: 0)
            ->and(value: $filterResult(contains: 'file'))
            ->toBeGreaterThan(expected: 0)
            ->and(value: $filterResult(contains: 'line'))
            ->toBeGreaterThan(expected: 0);
    });

    test(description: 'toMail includes trace information when available', closure: function (): void
    {
        $mail  = $this->notification->toMail(notifiable: testUser());
        $lines = collect(value: $mail->introLines);

        $traceStartCount = $lines->filter(
            callback: fn ($line) => is_string(value: $line) && str_contains($line, trans(key: 'exception.email.trace.start'))
        )->count();

        $traceEndCount = $lines->filter(
            callback: fn ($line) => is_string(value: $line) && str_contains($line, trans(key: 'exception.email.trace.end'))
        )->count();

        $traceLineCount  = $lines->filter(
            callback: fn ($line) => is_string(value: $line) && str_contains($line, basename(path: $this->exceptionData['trace'][0]['class']))
        )->count();

        expect(value: $traceStartCount)
            ->toBeGreaterThan(expected: 0)
            ->and(value: $traceEndCount)
            ->toBeGreaterThan(expected: 0)
            ->and(value: $traceLineCount)
            ->toBeGreaterThan(expected: 0);
    });

    test(description: 'toMail handles missing trace data', closure: function (): void
    {
        $exceptionDataWithoutTrace = $this->exceptionData;

        unset($exceptionDataWithoutTrace['trace']);

        $notification = new ExceptionReportNotification(exceptionData: $exceptionDataWithoutTrace);
        $mailMessage  = $notification->toMail(notifiable: testUser());
        $noTraceCount = collect($mailMessage->introLines)->filter(
            callback: fn ($line) => is_string(value: $line) && str_contains($line, trans(key: 'exception.email.trace.none'))
        )->count();

        expect(value: $noTraceCount)
            ->toBeGreaterThan(expected: 0);
    });

    test(description: 'toMail handles empty trace array', closure: function (): void
    {
        $exceptionDataWithEmptyTrace          = $this->exceptionData;
        $exceptionDataWithEmptyTrace['trace'] = [];

        $notification      = new ExceptionReportNotification(exceptionData: $exceptionDataWithEmptyTrace);
        $mailMessage       = $notification->toMail(notifiable: testUser());
        $traceRelatedCount = collect($mailMessage->introLines)->filter(callback: function ($line)
        {
            if (! is_string(value: $line))
            {
                return false;
            }

            return str_contains($line, trans(key: 'exception.email.trace.start')) || str_contains($line, trans(key: 'exception.email.trace.end')) || str_contains($line, trans(key: 'exception.email.trace.none'));
        })->count();

        expect(value: $traceRelatedCount)
            ->toBeGreaterThan(expected: 0);
    });

    test(description: 'toArray returns correct array', closure: function (): void
    {
        expect(value: $array = $this->notification->toArray(notifiable: testUser()))
            ->toBeArray()
            ->toHaveKey(key: 'exceptionData')
            ->and(value: $array['exceptionData'])
            ->toBe(expected: $this->exceptionData);
    });
});
