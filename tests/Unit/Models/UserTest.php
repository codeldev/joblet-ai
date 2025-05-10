<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Generated;
use App\Models\Order;
use App\Models\Usage;
use App\Models\User;
use App\Notifications\Auth\LoginLinkNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

test(description: 'to array', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new User)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'name',
        'email',
        'password',
        'remember_token',
        'cv_filename',
        'cv_content',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'created_at',
        'updated_at',
    ]);
});

test(description: 'user has correct hidden attributes', closure: function (): void
{
    expect(value: (new User)->getHidden())->toBe(expected: [
        'password',
        'remember_token',
    ]);
});

test(description: 'user has correct casts', closure: function (): void
{
    expect(value: (new User)->getCasts())->toBe(expected: [
        'password' => 'hashed',
    ]);
});

test(description: 'user has generated relationship', closure: function (): void
{
    expect(value: (new User)->generated()->getRelated())
        ->toBeInstanceOf(class: Generated::class);
});

test(description: 'user has orders relationship', closure: function (): void
{
    expect(value: (new User)->orders()->getRelated())
        ->toBeInstanceOf(class: Order::class);
});

test(description: 'user has usage relationship', closure: function (): void
{
    expect(value: (new User)->usage()->getRelated())
        ->toBeInstanceOf(class: Usage::class);
});

test(description: 'sendLoginLinkNotification sends notification', closure: function (): void
{
    Notification::fake();

    $user = User::factory()->create();
    $user->sendLoginLinkNotification();

    Notification::assertSentTo(
        notifiable: $user,
        notification: LoginLinkNotification::class
    );
});
