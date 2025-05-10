<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('can access the terms page as a guest', function (): void
{
    $this->get(uri: route(name: 'terms'))
        ->assertOk();
});

it('can access the terms page as a user', function (): void
{
    $this->actingAs(user: testUser())
        ->get(uri: route(name: 'terms'))
        ->assertOk();
});
