<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('can access the home page as a guest', function (): void
{
    $this->get(uri: route(name: 'home'))
        ->assertOk();
});

it('redirect to dashboard when accessing home page as a user', function (): void
{
    $this->actingAs(user: testUser())
        ->get(uri: route(name: 'home'))
        ->assertRedirectToRoute(name: 'dashboard');
});
