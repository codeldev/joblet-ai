<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Account\DeleteAction;
use App\Jobs\DeleteAccountJob;
use App\Models\User;

it('processes an account deletion request for a user', function (): void
{
    Bus::fake();

    $user = testUser();

    $this->actingAs(user: $user);

    expect(value: User::count())
        ->toBe(expected: 1);

    (new DeleteAction)->handle(
        success: fn () => $this->assertTrue(true)
    );

    Bus::assertDispatched(
        command: DeleteAccountJob::class
    );

    new DeleteAccountJob(userId: $user->id)->handle();

    expect(value: User::count())
        ->toBe(expected: 0)
        ->and(value: Auth::check())
        ->tobeFalse()
        ->and(value: Auth::user())
        ->toBeNull();
});

it('does not process an account deletion request for a guest', function (): void
{
    Bus::fake();

    $user = testUser();

    expect(value: User::count())
        ->toBe(expected: 1);

    (new DeleteAction)->handle(
        success: fn () => $this->assertTrue(true)
    );

    Bus::assertNotDispatched(
        command: DeleteAccountJob::class
    );

    expect(value: User::count())
        ->toBe(expected: 1);
});
