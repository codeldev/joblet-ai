<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Password;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->testUser  = testUser();
});

it('can render the update password component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.password.title'));
});

it('passes update password validation', function (): void
{
    $password = str()->random(20);

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', $password)
        ->set('form.confirmed', $password)
        ->call(method: 'submit')
        ->assertHasNoErrors();
});

it('fails update password validation when no new password given', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.confirmed', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('fails update password validation when new password is empty', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', '')
        ->set('form.confirmed', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('fails update password validation when no confirm password given', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.confirmed']);
});

it('fails update password validation when confirm password is empty', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', str()->random(20))
        ->set('form.confirmed', '')
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.confirmed']);
});

it('fails update password validation when new password is not the same as confirm password', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', str()->random(20))
        ->set('form.confirmed', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('fails update password validation when confirm password is not the same as new password', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Password::class)
        ->set('form.password', str()->random(20))
        ->set('form.confirmed', str()->random(20))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.confirmed']);
});
