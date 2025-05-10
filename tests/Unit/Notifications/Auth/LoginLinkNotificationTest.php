<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Notifications\Auth\LoginLinkNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

describe(description: 'LoginLinkNotification', tests: function (): void
{
    test(description: 'via returns mail channel', closure: function (): void
    {
        expect(value: (new LoginLinkNotification)->via(notifiable: testUser()))
            ->toBe(expected: ['mail']);
    });

    test(description: 'toMail returns correct MailMessage', closure: function (): void
    {
        URL::shouldReceive('temporarySignedRoute')
            ->once()
            ->andReturn('https://example.com/magic-login');

        $mail = (new LoginLinkNotification)->toMail(notifiable: testUser());

        expect(value: $mail)
            ->toBeInstanceOf(class: MailMessage::class)
            ->and(value: $mail->actionUrl)
            ->toBe(expected: 'https://example.com/magic-login');
    });
});
