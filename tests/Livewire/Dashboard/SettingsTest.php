<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Dashboard\Settings;
use App\Models\Generated;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

it('can render the letter settings component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Settings::class)
        ->assertOk()
        ->assertSee(values: 'show-settings');
});

it('displays the settings model when user owns the letter', function (): void
{
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Settings::class)
        ->call('view', $generated)
        ->assertSet(name: 'generated', value: $generated)
        ->assertDispatched(event: 'modal-show', name: 'show-settings');
});

it('displays an when trying to view settings of a letter not owned by the user', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Settings::class)
        ->call('view', Generated::factory()->create())
        ->assertSet(name: 'generated', value: null)
        ->assertNotDispatched(event: 'modal-show', name: 'show-settings')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('removes settings data when modal is closed', function (): void
{
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Settings::class)
        ->call('view', $generated)
        ->assertSet(name: 'generated', value: $generated)
        ->call('close')
        ->assertSet(name: 'generated', value: null);
});
