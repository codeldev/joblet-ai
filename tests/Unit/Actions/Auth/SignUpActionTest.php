<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Auth\SignUpAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(closure: function (): void
{
    $this->userData = [
        'name'     => fake()->name(),
        'email'    => testEmail(),
        'password' => str()->random(20),
        'agreed'   => true,
    ];
});

it('creates a new user account', function (): void
{
    expect(value: User::count())
        ->toBe(expected: 0);

    (new SignUpAction)->handle(
        validated: $this->userData,
        success  : fn () => $this->assertTrue(true),
        failed   : fn () => $this->assertTrue(false),
    );

    expect(value: User::count())
        ->toBe(expected: 1);
});

it('fails to create a new account due to database issue', function (): void
{
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(exception: new Exception(message: 'Database exception'));

    expect(value: User::count())
        ->toBe(expected: 0);

    (new SignUpAction)->handle(
        validated: $this->userData,
        success  : fn () => $this->assertTrue(false),
        failed   : fn () => $this->assertTrue(true),
    );

    expect(value: User::count())
        ->toBe(expected: 0);
});

it('fails to set user data properly', function (): void
{
    Hash::shouldReceive('make')
        ->once()
        ->andThrow(exception: new Exception(message: 'Test hashing exception'));

    expect(value: User::count())
        ->toBe(expected: 0);

    (new SignUpAction)->handle(
        validated: $this->userData,
        success  : fn () => $this->assertTrue(false),
        failed   : fn () => $this->assertTrue(true),
    );

    expect(value: User::count())
        ->toBe(expected: 0);
});
