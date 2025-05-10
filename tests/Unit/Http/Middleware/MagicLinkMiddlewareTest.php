<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

describe(description: 'MagicLinkMiddleware', tests: function (): void
{
    it('redirects authenticated user to dashboard with error message on invalid signature', function (): void
    {
        $magicLink = URL::temporarySignedRoute(
            name      : 'magic',
            expiration: now()->addMinutes(value: 15),
            parameters: [
                'id'   => Str::uuid()->toString(),
                'hash' => sha1(string: testEmail()),
            ]
        );

        $this->get(uri: $magicLink)
            ->assertRedirectToRoute(name: 'home');

        expect(value: Session::get(key: 'app-message'))->toBe(expected: [
            'type'    => 'error',
            'message' => 'auth.sign.in.link.invalid',
        ]);
    });

    it('redirects guest to home with expired message on invalid signature', function (): void
    {
        $magicLink = URL::temporarySignedRoute(
            name      : 'magic',
            expiration: now()->subMinutes(value: 20),
            parameters: [
                'id'   => $this->testUser->id,
                'hash' => sha1(string: $this->testUser->email),
            ]
        );

        $this->get(uri: $magicLink)
            ->assertRedirectToRoute(name: 'home');

        expect(value: Session::get(key: 'app-message'))->toBe(expected: [
            'type'    => 'error',
            'message' => 'auth.sign.in.link.expired',
        ]);
    });

    it('calls parent handle on valid signature', function (): void
    {
        $magicLink = URL::temporarySignedRoute(
            name      : 'magic',
            expiration: now()->addMinutes(value: 15),
            parameters: [
                'id'   => $this->testUser->id,
                'hash' => sha1(string: $this->testUser->email),
            ]
        );

        expect(value: Auth::check())
            ->toBeFalse();

        $this->get(uri: $magicLink)
            ->assertRedirectToRoute(name: 'dashboard');

        expect(value: Auth::check())
            ->toBeTrue()
            ->and(value: Session::get(key: 'app-message'))
            ->toBe(expected: [
                'type'    => 'success',
                'message' => 'auth.sign.in.link.success',
            ]);
    });

    it('redirects to dashboard if user is already logged in', function (): void
    {
        $magicLink = URL::temporarySignedRoute(
            name      : 'magic',
            expiration: now()->addMinutes(value: 15),
            parameters: [
                'id'   => $this->testUser->id,
                'hash' => sha1(string: $this->testUser->email),
            ]
        );

        $this->actingAs(user: $this->testUser)
            ->get(uri: $magicLink)
            ->assertRedirectToRoute(name: 'dashboard');
    });
});
