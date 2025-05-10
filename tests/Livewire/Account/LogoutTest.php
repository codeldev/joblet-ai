<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Logout;
use Livewire\Livewire;

it('can render the logout component', function (): void
{
    Livewire::actingAs(user: testUser())
        ->test(name: Logout::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.logout.title'));
});

it('can process a logout request', function (): void
{
    Livewire::actingAs(user: testUser())
        ->test(name: Logout::class)
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'account.logout.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        )
        ?->assertDispatched(
            event   : 'redirect',
            timeout : 3750,
            redirect: route(name: 'auth'),
        );
});
