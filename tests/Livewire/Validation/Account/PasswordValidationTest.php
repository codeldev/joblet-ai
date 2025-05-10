<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Password;
use Livewire\Livewire;

describe(description: 'Account/Password Validation', tests: function (): void
{
    test(description: 'it fails if password is empty', closure: function (): void
    {
        Livewire::test(name: Password::class)
            ->set(name: 'form.password', value: '')
            ->set(name: 'form.confirmed', value: 'some-password')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'required']);
    });

    test(description: 'it fails if password is not equal to confirmed', closure: function (): void
    {
        Livewire::test(name: Password::class)
            ->set(name: 'form.password', value: 'password1')
            ->set(name: 'form.confirmed', value: 'password2')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'same']);
    });

    test(description: 'it fails if confirmed is empty', closure: function (): void
    {
        Livewire::test(name: Password::class)
            ->set(name: 'form.password', value: 'some-password')
            ->set(name: 'form.confirmed', value: '')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.confirmed' => 'required']);
    });

    test(description: 'it fails if confirmed is not equal to password', closure: function (): void
    {
        Livewire::test(name: Password::class)
            ->set(name: 'form.password', value: 'password1')
            ->set(name: 'form.confirmed', value: 'password2')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.confirmed' => 'same']);
    });
});
