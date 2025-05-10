<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Auth\SignUp;
use Illuminate\Support\Str;
use Livewire\Livewire;

describe(description: 'Auth/SignUp Validation', tests: function (): void
{
    test(description: 'it fails if name is empty', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: '')
            ->set(name: 'form.email', value: 'user@example.com')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.name' => 'required']);
    });

    test(description: 'it fails if name is longer than 255 characters', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: Str::random(length: 256))
            ->set(name: 'form.email', value: 'user@example.com')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.name' => 'max']);
    });

    test(description: 'it fails if email is empty', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: '')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'required']);
    });

    test(description: 'it fails if email is not lowercase', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'USER@EXAMPLE.COM')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'lowercase']);
    });

    test(description: 'it fails if email is not a valid email', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'not-an-email')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'email']);
    });

    test(description: 'it fails if email is longer than 255 characters', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: str_repeat(string: 'a', times: 246) . '@example.com')
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'max']);
    });

    test(description: 'it fails if email is not unique', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: testUser()->email)
            ->set(name: 'form.password', value: 'password123')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: 'form.email');
    });

    test(description: 'it fails if password is empty', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'user@example.com')
            ->set(name: 'form.password', value: '')
            ->set(name: 'form.agreed', value: true)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'required']);
    });

    test(description: 'it fails if terms not agreed', closure: function (): void
    {
        Livewire::test(name: SignUp::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'user@example.com')
            ->set(name: 'form.password', value: '')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'required']);
    });
});
