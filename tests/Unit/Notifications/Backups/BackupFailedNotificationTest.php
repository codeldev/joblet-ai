<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Notifications\Backups\BackupFailedNotification;
use Illuminate\Notifications\Messages\MailMessage;

describe(description: 'BackupFailedNotification', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        $this->errorMessage = fake()->sentence();
        $this->notification = new BackupFailedNotification(errorMessage: $this->errorMessage);
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

    test(description: 'toArray returns correct array', closure: function (): void
    {
        expect(value: $array = $this->notification->toArray(notifiable: testUser()))
            ->toBeArray()
            ->toHaveKey(key: 'error_message')
            ->and(value: $array['error_message'])
            ->toBe(expected: $this->errorMessage);
    });
});
