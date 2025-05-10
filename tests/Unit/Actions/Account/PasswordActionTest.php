<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Account\PasswordAction;

it('updates a password for a user', function (): void
{
    $testUser = testUser();
    $password = str()->random(20);

    $this->actingAs(user: $testUser);

    (new PasswordAction)->handle(
        password: $password,
        success: function () use ($testUser, $password): void
        {
            expect(value: Hash::check($password, $testUser->fresh()->password))
                ->toBeTrue();
        }
    );
});
