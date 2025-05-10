<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Contact\SendContactAction;
use App\Models\Message;
use App\Notifications\Contact\ContactMessageNotification;
use App\Services\Models\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

beforeEach(closure: function (): void
{
    $this->validated = [
        'name'    => fake()->name(),
        'email'   => fake()->safeEmail(),
        'message' => fake()->paragraph(),
    ];

    $this->action = new SendContactAction;

    Notification::fake();
});

describe(description: 'Send Contact Message Action', tests: function (): void
{
    it(description: 'stores message and sends email on success', closure: function (): void
    {
        expect(value: Message::count())
            ->toBe(expected: 0);

        $this->action->handle(
            validated: $this->validated,
            success  : fn () => expect(true)->toBeTrue(),
            failed   : fn () => null,
        );

        expect(value: Message::count())
            ->toBe(expected: 1);

        Notification::assertSentTo(
            notifiable  : UserService::getSupportUser(),
            notification: ContactMessageNotification::class,
            callback    : fn (ContactMessageNotification $notification) => $notification->contactData === $this->validated
        );
    });

    it(description: 'returns error on database failure', closure: function (): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new Exception(message: 'Database error'));

        expect(value: Message::count())
            ->toBe(expected: 0);

        $this->action->handle(
            validated: $this->validated,
            success  : fn () => null,
            failed   : fn () => expect(value: true)->toBeTrue(),
        );

        expect(value: Message::count())
            ->toBe(expected: 0);

        Notification::assertNothingSent();
    });

    it(description: 'stores email data', closure: function (): void
    {
        expect(value: Message::count())
            ->toBe(expected: 0);

        $reflection = new ReflectionClass(objectOrClass: $this->action);
        $method     = $reflection->getMethod(name: 'storeMessage');

        $method->setAccessible(accessible: true);
        $method->invoke($this->action, $this->validated);

        expect(value: Message::count())
            ->toBe(expected: 1);
    });

    it(description: 'does not store email data', closure: function (): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new Exception(message: 'Database error'));

        expect(value: Message::count())
            ->toBe(expected: 0);

        $reflection = new ReflectionClass(objectOrClass: $this->action);
        $method     = $reflection->getMethod(name: 'storeMessage');

        $method->setAccessible(accessible: true);

        expect(value: fn () => $method->invoke($this->action, $this->validated))
            ->toThrow(exception: Exception::class);
    });

    it(description: 'sends email notification', closure: function (): void
    {
        $reflection = new ReflectionClass(objectOrClass: $this->action);
        $method     = $reflection->getMethod(name: 'sendEmail');

        $method->setAccessible(accessible: true);
        $method->invoke($this->action, $this->validated);

        Notification::assertSentTo(
            notifiable  : UserService::getSupportUser(),
            notification: ContactMessageNotification::class,
            callback    : fn (ContactMessageNotification $notification) => $notification->contactData === $this->validated
        );
    });
});
