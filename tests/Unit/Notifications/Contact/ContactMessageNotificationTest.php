<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Notifications\Contact\ContactMessageNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

describe(description: 'ContactMessageNotification', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        $this->contactData = [
            'name'    => fake()->name(),
            'email'   => fake()->safeEmail(),
            'message' => fake()->paragraph(),
        ];

        $this->notification = new ContactMessageNotification(
            contactData: $this->contactData
        );
    });

    test(description: 'via returns mail channel', closure: function (): void
    {
        expect(value: $this->notification->via(notifiable: testUser()))
            ->toBe(expected: ['mail']);
    });

    test(description: 'toMail returns correct MailMessage', closure: function (): void
    {
        expect(value: $mailMessage = $this->notification->toMail(notifiable: testUser()))
            ->toBeInstanceOf(class: MailMessage::class)
            ->and(value: $mailMessage->replyTo)
            ->toBeArray()
            ->and(value: $mailMessage->replyTo[0][0])
            ->toBe(expected: $this->contactData['email'])
            ->and(value: $mailMessage->replyTo[0][1])
            ->toBe(expected: $this->contactData['name']);
    });

    test(description: 'toMail contains correct content', closure: function (): void
    {
        $mailMessage = $this->notification->toMail(notifiable: testUser());
        $appName     = config(key: 'app.name');

        expect(value: $mailMessage->subject)
            ->toContain(needle: $appName)
            ->and(value: $lines = $mailMessage->introLines)
            ->toBeArray()
            ->toHaveCount(count: 5)
            ->and(value: $lines[0])
            ->toContain(needle: $appName)
            ->and(value: $lines[1])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[1])
            ->toContain(needle: $this->contactData['name'])
            ->and(value: $lines[2])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[2])
            ->toContain(needle: $this->contactData['email'])
            ->and(value: $lines[3])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[3])
            ->toBe(expected: '<hr />')
            ->and(value: $lines[4])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[4])
            ->toContain(needle: nl2br(string: $this->contactData['message']));
    });

    test(description: 'toArray returns correct array', closure: function (): void
    {
        expect(value: $array = $this->notification->toArray(notifiable: testUser()))
            ->toBeArray()
            ->toHaveKey(key: 'contactData')
            ->and(value: $array['contactData'])
            ->toBe(expected: $this->contactData);
    });
});
