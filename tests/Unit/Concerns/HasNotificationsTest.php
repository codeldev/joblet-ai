<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use Livewire\Livewire;
use Tests\Classes\Unit\Concerns\HasNotificationsTest;

beforeEach(closure: function (): void
{
    $this->redirectTo    = route(name: 'dashboard');
    $this->notifyMessage = fake()->sentence(nbWords: 5);
    $this->variants      = collect(value: [
        'notifySuccess' => 'success',
        'notifyError'   => 'danger',
        'notifyWarning' => 'warning',
        'notifyInfo'    => 'info',
    ]);
});

it('dispatches notifications', function (): void
{
    $this->variants->each(callback: function ($variant, $method): void
    {
        Livewire::test(name: HasNotificationsTest::class)
            ->call($method, $this->notifyMessage)
            ->assertDispatched(
                event   : 'toast-show',
                duration: 3500,
                slots   : [
                    'text' => $this->notifyMessage,
                ],
                dataset : [
                    'variant' => $variant,
                ]
            );
    });
});

it('dispatches redirects with Notifications', function (): void
{
    $this->variants->each(callback: function ($variant, $method): void
    {
        Livewire::test(name: HasNotificationsTest::class)
            ->call($method, $this->notifyMessage, $this->redirectTo)
            ->assertDispatched(
                event   : 'redirect',
                timeout : 3750,
                redirect: $this->redirectTo,
            );
    });
});
