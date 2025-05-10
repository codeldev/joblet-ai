<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Auth\MagicLinkAction;
use App\Notifications\Auth\LoginLinkNotification;
use Illuminate\Support\Facades\Notification;

it('sends a magic link for a valid user', function (): void
{
    Notification::fake();

    $user = testUser();

    (new MagicLinkAction)->handle(
        validated: ['email' => $user->email],
        callback : fn () => null
    );

    Notification::assertSentTo(
        notifiable: $user,
        notification: LoginLinkNotification::class
    );
});

it('does not find a valid user and does not send an email', function (): void
{
    Notification::fake();

    (new MagicLinkAction)->handle(
        validated: ['email' => testEmail()],
        callback : fn () => null
    );

    Notification::assertNothingSent();
});
