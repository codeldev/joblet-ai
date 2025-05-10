<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Dashboard\Delete;
use App\Models\Generated;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

it('can render the delete component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'letter.delete.modal.title'));
});

it('shows the confirmation modal on delete of a letter', function (): void
{
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->call(method: 'confirm', generated: $generated)
        ->assertSet(name: 'generated.id', value: $generated->id)
        ->assertDispatched(event: 'modal-show', name: 'confirm-deletable');
});

it('deletes a letter', function (): void
{
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    expect(value: Generated::count())
        ->toBe(expected: 1);

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->call(method: 'confirm', generated: $generated)
        ->call(method: 'delete')
        ->assertSet(name: 'generated', value: null)
        ->assertDispatched(event: 'modal-close', name: 'confirm-deletable')
        ->assertDispatched(event: 'reload-dashboard')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'letter.delete.modal.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        );

    expect(value: Generated::count())
        ->toBe(expected: 0);
});

it('cancels letter deletion', function (): void
{
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->call(method: 'confirm', generated: $generated)
        ->assertSet(name: 'generated.id', value: $generated->id)
        ->call(method: 'cancel')
        ->assertSet(name: 'generated', value: null);
});

it('cannot delete another users letter', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->call(method: 'confirm', generated: Generated::factory()->create())
        ->call(method: 'delete')
        ->assertSet(name: 'generated', value: null)
        ->assertDispatched(event: 'modal-close', name: 'confirm-deletable')
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
