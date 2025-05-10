<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Contact\Index;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

describe(description: 'Contact livewire component', tests: function (): void
{
    it('can render the contact model component', function (): void
    {
        Livewire::test(name: Index::class)
            ->assertSee(values: trans(key: 'messages.contact.title'));
    });

    it('populates form fields when a user is logged in', function (): void
    {
        Livewire::actingAs(user: $user = testUser())
            ->test(name: Index::class)
            ->assertSet(name: 'form.name', value: $user->name)
            ->assertSet(name: 'form.email', value: $user->email);
    });

    it('does not populate form fields for a guest user', function (): void
    {
        Livewire::test(name: Index::class)
            ->assertSet(name: 'form.name', value: null)
            ->assertSet(name: 'form.email', value: null);
    });

    it('successfully submits the form and sends a message', function (): void
    {
        Livewire::actingAs(user: $user = testUser())
            ->test(name: Index::class)
            ->set('form.message', fake()->paragraph())
            ->call(method: 'submit')
            ->assertDispatched(event: 'modal-close', name: 'contact-form')
            ->assertDispatched(
                event   : 'toast-show',
                duration: 3500,
                slots   : [
                    'text' => trans(key: 'messages.contact.success'),
                ],
                dataset : [
                    'variant'  => 'success',
                ]
            )
            ->assertSet(name: 'form.name', value: $user->name)
            ->assertSet(name: 'form.email', value: $user->email)
            ->assertSet(name: 'form.message', value: null);
    });

    it('shows error on failure to send message', function (): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new Exception(message: 'Database error'));

        Livewire::actingAs(user: testUser())
            ->test(name: Index::class)
            ->set('form.message', fake()->paragraph())
            ->call(method: 'submit')
            ->assertDispatched(
                event   : 'toast-show',
                duration: 3500,
                slots   : [
                    'text' => trans(key: 'messages.contact.failed'),
                ],
                dataset : [
                    'variant'  => 'danger',
                ]
            );
    });

    it('sets the form to its original state on modal close', function (): void
    {
        Livewire::actingAs(user: $user = testUser())
            ->test(name: Index::class)
            ->assertSet(name: 'form.name', value: $user->name)
            ->assertSet(name: 'form.email', value: $user->email)
            ->set([
                'form.name'  => null,
                'form.email' => null,
            ])
            ->assertSet(name: 'form.name', value: null)
            ->assertSet(name: 'form.email', value: null)
            ->call(method: 'close')
            ->assertSet(name: 'form.name', value: $user->name)
            ->assertSet(name: 'form.email', value: $user->email);
    });

    it('opens the contact modal when flag is set in url', function (): void
    {
        Livewire::withQueryParams(params: ['contact' => 1])
            ->test(name: Index::class)
            ->call(method: 'onLoad')
            ->assertDispatched(event: 'modal-show', name: 'contact-form');
    });
});
