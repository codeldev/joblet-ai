<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Auth\Index;
use App\Livewire\Auth\SignIn;
use App\Notifications\Auth\LoginLinkNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('redirects and shows locked out after too many attempts', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set([
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
            'form.remember' => true,
        ])
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->assertRedirectToRoute(name: 'auth');

    Livewire::test(name: Index::class)
        ->assertSee(values: trans(key: 'auth.lockout.title'));
});

it('successfully logs in a valid user', function (): void
{
    $password = str()->random(20);
    $testUser = testUser(params: [
        'password' => Hash::make(value: $password),
    ]);

    expect(value: Auth::check())
        ->toBeFalse();

    Livewire::test(name: SignIn::class)
        ->set([
            'form.email'    => $testUser->email,
            'form.password' => $password,
        ])
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.in.success'),
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

    expect(value: Auth::check())
        ->toBeTrue();
});

test(description: 'lockout timer is used when user is locked out from log in', closure: function (): void
{
    Livewire::test(name: SignIn::class)
        ->set([
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
        ])
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->call(method: 'submit')
        ->assertRedirectToRoute(name: 'auth');

    Livewire::test(name: Index::class)
        ->call(method: 'lockoutTimer')
        ->assertSee(values: trans(key: 'auth.lockout.title'));

    Carbon::setTestNow(testNow: now()->copy()->addMinutes(value: 20));

    Livewire::test(name: Index::class)
        ->call(method: 'lockoutTimer')
        ->assertRedirect(uri: route(name: 'auth'));

    Carbon::setTestNow();
});

it('shows an error if email is not given', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set('form.password', str()->random(8))
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if email is not valid', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set([
            'form.email'    => 'not-an-email',
            'form.password' => str()->random(8),
        ])
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.email']);
});

it('shows an error if password not given', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set('form.email', testEmail())
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password']);
});

it('shows an error if no account found', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set([
            'form.email'    => testEmail(),
            'form.password' => str()->random(8),
            'form.remember' => true,
        ])
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.in.failed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('sends a magic link and shows success', function (): void
{
    Notification::fake();

    $user = testUser();

    Livewire::test(name: SignIn::class)
        ->set('form.email', $user->email)
        ->call(method: 'magicLink')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.in.forgot.sent'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        )->assertDispatched(event: 'reset-sign-in');

    Notification::assertSentTo(
        notifiable: $user,
        notification: LoginLinkNotification::class
    );
});

it('doesnt send a magic link but shows success', function (): void
{
    $user = testUser();

    Notification::fake();

    Livewire::test(name: SignIn::class)
        ->set('form.email', testEmail())
        ->call(method: 'magicLink')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'auth.sign.in.forgot.sent'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        )->assertDispatched(event: 'reset-sign-in');

    Notification::assertNotSentTo(
        notifiable: $user,
        notification: LoginLinkNotification::class
    );
});

it('gives an error when no email is given when trying to send a magic link', function (): void
{
    Livewire::test(name: SignIn::class)
        ->call(method: 'magicLink')
        ->assertHasErrors(keys: ['form.email']);
});

it('resets the form when clear is called', function (): void
{
    Livewire::test(name: SignIn::class)
        ->set('form.email', 'user@example.com')
        ->set('form.password', 'password123')
        ->set('form.remember', true)
        ->call(method: 'clear')
        ->assertSet(name: 'form.email', value: null)
        ->assertSet(name: 'form.password', value: null)
        ->assertSet(name: 'form.remember', value: true);
});
