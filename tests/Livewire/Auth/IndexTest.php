<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Auth\Index;

it('can access the sign in page as a guest', function (): void
{
    $this->get(uri: route(name: 'auth'))
        ->assertOk();
});

it('redirects to dashboard when accessing the sign in page as a user', function (): void
{
    $this->actingAs(user: testUser())
        ->get(uri: route(name: 'auth'))
        ->assertRedirectToRoute(name: 'dashboard');
});

it('clears any login data when switching to sign up', function (): void
{
    Livewire::test(Index::class)
        ->set('type', 'sign-in')
        ->assertDispatched('reset-sign-up');
});

it('clears any sign up data when switching to sign up', function (): void
{
    Livewire::test(Index::class)
        ->set('type', 'sign-up')
        ->assertDispatched('reset-sign-in');
});
