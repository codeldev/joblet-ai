<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Auth\SignUp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('registers a new user successfully', function (): void
{
    expect(value: User::count())
        ->toBe(expected: 0)
        ->and(value: Auth::check())
        ->toBeFalse();

    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => testEmail(),
            'form.password' => str()->random(20),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.up.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        )
        ?->assertDispatched(
            event   : 'redirect',
            timeout : 3750,
            redirect: route(name: 'dashboard'),
        );

    expect(value: User::count())
        ->toBe(expected: 1)
        ->and(value: Auth::check())
        ->toBeTrue();
});

it('shows a lockout message when too many attempts within an hour', function (): void
{
    $testEmail    = testEmail();
    $password     = str()->random(20);
    $emailHash    = mb_substr(md5(string: Str::lower(value: $testEmail)), 0, 8);
    $rateLimitKey = Str::transliterate(string: "signup|127.0.0.1|{$emailHash}");

    for ($i = 1; $i <= 5; $i++)
    {
        RateLimiter::hit(key: $rateLimitKey);
    }

    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => $testEmail,
            'form.password' => $password,
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            dataset : [
                'variant'  => 'danger',
            ]
        );

    RateLimiter::clear(key: $rateLimitKey);
});

it('gives validation errors on registration', function (): void
{
    expect(value: User::count())
        ->toBe(expected: 0)
        ->and(value: Auth::check())
        ->toBeFalse();

    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'  => fake()->name(),
            'form.email' => testEmail(),
        ])
        ->call(method: 'submit')
        ->assertHasErrors();

    expect(value: User::count())
        ->toBe(expected: 0)
        ->and(value: Auth::check())
        ->toBeFalse();
});

it('shows an error message when signup process encounters an exception', function (): void
{
    Hash::shouldReceive('make')
        ->once()
        ->andThrow(exception: new Exception(message: 'Test hashing exception'));

    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => testEmail(),
            'form.password' => str()->random(20),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.up.failed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('shows an error if name is not given', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.name']);
});

it('shows an error if name is empty', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => '',
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.name']);
});

it('shows an error if name is more than 255 characters', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->realTextBetween(minNbChars: 256, maxNbChars: 300),
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.name']);
});

it('shows an error if email is not given', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if email is empty', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => '',
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if email is not valid', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => 'invalid-email',
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if email is more than 255 chars', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => str()->random(250) . '@gmail.com',
            'form.password' => str()->random(8),
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if password not given', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'   => fake()->name(),
            'form.email'  => testEmail(),
            'form.agreed' => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('shows an error if is password is empty', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => testEmail(),
            'form.password' => '',
            'form.agreed'   => true,
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('shows an error if not agreed to term', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set([
            'form.name'     => fake()->name(),
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.agreed']);
});

it('resets the form when clear is called', function (): void
{
    Livewire::test(name: SignUp::class)
        ->set('form.name', 'Test User')
        ->set('form.email', 'user@example.com')
        ->set('form.password', 'password123')
        ->set('form.agreed', true)
        ->call(method: 'clear')
        ->assertSet(name: 'form.name', value: null)
        ?->assertSet(name: 'form.email', value: null)
        ?->assertSet(name: 'form.password', value: null)
        ?->assertSet(name: 'form.agreed', value: false);
});
