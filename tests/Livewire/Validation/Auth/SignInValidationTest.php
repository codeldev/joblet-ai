<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Auth\SignIn;
use Livewire\Livewire;

describe(description: 'Auth/SignIn Validation', tests: function (): void
{
    test(description: 'it fails if email is empty', closure: function (): void
    {
        Livewire::test(name: SignIn::class)
            ->set(name: 'form.email', value: '')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.remember', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'required']);
    });

    test(description: 'it fails if email is not a valid email', closure: function (): void
    {
        Livewire::test(name: SignIn::class)
            ->set(name: 'form.email', value: 'not-an-email')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.remember', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'email']);
    });

    test(description: 'it fails if email is not lowercase', closure: function (): void
    {
        Livewire::test(name: SignIn::class)
            ->set(name: 'form.email', value: 'USER@EXAMPLE.COM')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.remember', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'email']);
    });

    test(description: 'it fails if password is empty', closure: function (): void
    {
        Livewire::test(name: SignIn::class)
            ->set(name: 'form.email', value: 'user@example.com')
            ->set(name: 'form.password', value: '')
            ->set(name: 'form.remember', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'required']);
    });
});
