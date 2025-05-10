<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Delete;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->testPassword = str()->random(20);

    $this->testUser  = testUser(params: [
        'password' => Hash::make(value: $this->testPassword),
    ]);
});

it('can render the delete account component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.delete.title'));
});

it('passes delete account validation', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->set('form.password', $this->testPassword)
        ->call(method: 'submit')
        ->assertHasNoErrors()
        ?->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'account.delete.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        );
});

it('fails delete account validation', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->set('form.password', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('cancel method resets form and clears validation', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Delete::class)
        ->set('form.password', '')
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password'])
        ?->call(method: 'cancel')
        ->assertHasNoErrors()
        ?->assertSet(name: 'form.password', value: null);
});
