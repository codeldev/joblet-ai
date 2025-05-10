<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Jobs\DeleteAccountJob;
use App\Models\User;

test(description: 'it deletes a user account', closure: function (): void
{
    $user = testUser();

    expect(value: User::count())->toBe(expected: 1);

    new DeleteAccountJob(userId: $user->id)->handle();

    expect(value: User::count())->toBe(expected: 0);
});
