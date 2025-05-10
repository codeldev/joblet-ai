<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Account\LogoutAction;

it('can processes a logout request', function (): void
{
    $this->actingAs(user: testUser());

    (new LogoutAction)->handle();

    expect(value: Auth::check())
        ->tobeFalse()
        ->and(value: Auth::user())
        ->toBeNull();
});
