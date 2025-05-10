<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Index;
use Livewire\Livewire;

it('cannot access the account settings page as a guest', function (): void
{
    $this->get(uri: route(name: 'account'))
        ->assertRedirectToRoute(name: 'auth');
});

it('can access the account settings page as a user', function (): void
{
    $response = $this->actingAs(user: testUser())
        ->get(uri: route(name: 'account'))
        ->assertOk();

    expect(value: $response->getContent())
        ->toContain(needles: 'wire:id')
        ->toContain(needles: 'account.order-payment')
        ->toContain(needles: 'account.profile')
        ->toContain(needles: 'account.password')
        ->toContain(needles: 'account.delete')
        ->toContain(needles: 'account.logout');
});

it('can render the account page', function (): void
{
    Livewire::actingAs(user: testUser())
        ->test(name: Index::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.title'));
});
