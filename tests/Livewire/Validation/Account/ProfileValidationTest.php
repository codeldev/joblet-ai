<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Profile;
use Illuminate\Support\Str;
use Livewire\Livewire;

describe(description: 'Account/Profile Validation', tests: function (): void
{
    test(description: 'it fails if name is empty', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: '')
            ->set(name: 'form.email', value: 'user@example.com')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.name' => 'required']);
    });

    test(description: 'it fails if name is longer than 255 characters', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: Str::random(length: 256))
            ->set(name: 'form.email', value: 'user@example.com')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.name' => 'max']);
    });

    test(description: 'it fails if email is empty', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: '')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'required']);
    });

    test(description: 'it fails if email is not a valid email', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'not-an-email')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'email']);
    });

    test(description: 'it fails if email is not lowercase', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: 'TEST-USER@EXAMPLE.COM')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'email']);
    });

    test(description: 'it fails if email is longer than 255 characters', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: str_repeat(string: 'a', times: 246) . '@example.com')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'max']);
    });

    test(description: 'it fails if email is not unique', closure: function (): void
    {
        Livewire::actingAs(user: testUser())
            ->test(name: Profile::class)
            ->set(name: 'form.name', value: 'Test User')
            ->set(name: 'form.email', value: testUser()->email)
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.email' => 'unique']);
    });
});
