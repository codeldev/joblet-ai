<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Profile;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

it('can render the update profile component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->assertOk()
        ->assertSee(values: trans(key: 'account.profile.title'));
});

it('passes update profile validation', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => testEmail(),
        ])
        ->call(method: 'submit')
        ->assertHasNoErrors()
        ?->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'account.profile.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        );
});

it('passes profile validation when name is not changed', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set('form.email', testEmail())
        ->call(method: 'submit')
        ->assertHasNoErrors();
});

it('fails update profile validation with empty name', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => '',
            'form.email' => testEmail(),
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.name']);
});

it('fails update profile validation with too long a name', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => str()->random(260),
            'form.email' => testEmail(),
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.name']);
});

it('passes update profile validation when email is not changed', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set('form.name', fake()->name())
        ->call(method: 'submit')
        ->assertHasNoErrors();
});

it('fails update profile validation with empty email', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => '',
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('fails update profile validation with an invalid email', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => 'invalid-email',
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('fails update profile validation with too long an email', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => str()->random(260) . '@gmail.com',
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('fails validation when an email is already taken', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Profile::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => testUser()->email,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});
