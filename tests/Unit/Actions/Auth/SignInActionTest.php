<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Auth\SignInAction;

it('logs a user in ', function (): void
{
    $user = testUser(
        params: ['password' => $password = str()->random(20)]
    );

    $credentials = [
        'email'    => $user->email,
        'password' => $password,
        'remember' => true,
    ];

    (new SignInAction)->handle(
        credentials: $credentials,
        success    : fn () => $this->assertTrue(true),
        failed     : fn () => $this->assertTrue(false),
    );
});

it('does not log a user in ', function (): void
{
    $credentials = [
        'email'    => testEmail(),
        'password' => str()->random(20),
        'remember' => true,
    ];

    (new SignInAction)->handle(
        credentials: $credentials,
        success    : fn () => $this->assertTrue(false),
        failed     : fn () => $this->assertTrue(true),
    );
});
