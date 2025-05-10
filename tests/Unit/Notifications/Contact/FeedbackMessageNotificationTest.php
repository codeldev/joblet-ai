<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Notifications\Contact\FeedbackMessageNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

describe(description: 'FeedbackMessageNotification', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        $this->feedbackData = [
            'name'    => fake()->name(),
            'email'   => fake()->safeEmail(),
            'message' => fake()->paragraph(),
        ];

        $this->notification = new FeedbackMessageNotification(
            feedbackData: $this->feedbackData
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
            ->toBe(expected: $this->feedbackData['email'])
            ->and(value: $mailMessage->replyTo[0][1])
            ->toBe(expected: $this->feedbackData['name']);
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
            ->toContain(needle: $this->feedbackData['name'])
            ->and(value: $lines[2])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[2])
            ->toContain(needle: $this->feedbackData['email'])
            ->and(value: $lines[3])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[3])
            ->toBe(expected: '<hr />')
            ->and(value: $lines[4])
            ->toBeInstanceOf(class: HtmlString::class)
            ->and(value: (string) $lines[4])
            ->toContain(needle: nl2br(string: $this->feedbackData['message']));
    });

    test(description: 'toMail handles empty name and email values', closure: function (): void
    {
        $incompleteData = [
            'name'    => '',
            'email'   => '',
            'message' => fake()->paragraph(),
        ];

        $notification = new FeedbackMessageNotification(
            feedbackData: $incompleteData
        );

        $mailMessage  = $notification->toMail(notifiable: testUser());

        expect(value: (string) $mailMessage->introLines[1])
            ->toContain(needle: trans(key: 'messages.feedback.mail.empty'))
            ->and(value: (string) $mailMessage->introLines[2])
            ->toContain(needle: trans(key: 'messages.feedback.mail.empty'));

        $mailMessage->replyTo === null
            ? expect(value: $mailMessage->replyTo)->toBeNull()
            : expect(value: $mailMessage->replyTo)->toBeArray();
    });

    test(description: 'toMail handles empty name but has valid email', closure: function (): void
    {
        $partialData = [
            'name'    => '',
            'email'   => fake()->safeEmail(),
            'message' => fake()->paragraph(),
        ];

        $notification = new FeedbackMessageNotification(
            feedbackData: $partialData
        );

        $mailMessage  = $notification->toMail(notifiable: testUser());

        expect(value: (string) $mailMessage->introLines[1])
            ->toContain(needle: trans(key: 'messages.feedback.mail.empty'))
            ->and(value: $mailMessage->replyTo)
            ->toBeArray()
            ->and(value: $mailMessage->replyTo[0][0])
            ->toBe(expected: $partialData['email'])
            ->and(value: $mailMessage->replyTo[0][1])
            ->toBeString();
    });

    test(description: 'toArray returns correct array', closure: function (): void
    {
        expect(value: $array = $this->notification->toArray(notifiable: testUser()))
            ->toBeArray()
            ->toHaveKey(key: 'feedbackData')
            ->and(value: $array['feedbackData'])
            ->toBe(expected: $this->feedbackData);
    });
});
