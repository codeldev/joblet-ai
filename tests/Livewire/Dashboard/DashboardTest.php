<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Dashboard\Index;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

it('cannot access the dashboard page as a guest', function (): void
{
    $this->get(uri: route(name: 'dashboard'))
        ->assertRedirectToRoute(name: 'auth');
});

it('can access the dashboard page as a user', function (): void
{
    $this->actingAs(user: testUser())
        ->get(uri: route(name: 'dashboard'))
        ->assertOk();
});

it('can render the dashboard page', function (): void
{
    Livewire::actingAs(user: testUser())
        ->test(name: Index::class)
        ->assertOk()
        ->assertSee(values: 'displayMessages');
});

it('displays messages after rendering the dashboard page', function (): void
{
    $messages = [
        'success' => [
            'type'     => 'success',
            'message'  => 'A success message',
            'redirect' => null,
        ],
        'error' => [
            'type'     => 'danger',
            'message'  => 'An error message',
            'redirect' => null,
        ],
        'warning' => [
            'type'     => 'warning',
            'message'  => 'A warning message',
            'redirect' => null,
        ],
        'info' => [
            'type'     => 'info',
            'message'  => 'An info message',
            'redirect' => null,
        ],
    ];

    collect(value: $messages)->each(callback: function (array $message, string $type): void
    {
        $messenger         = $message;
        $messenger['type'] = $type;

        Session::put('app-message', $messenger);

        Livewire::actingAs(user: testUser())
            ->test(name: Index::class)
            ->call(method: 'displayMessages')
            ->assertDispatched(
                event   : 'toast-show',
                duration: 3500,
                slots   : [
                    'text' => $message['message'],
                ],
                dataset : [
                    'variant'  => $message['type'],
                ]
            );
    });
});

it('does not display a message after rendering the dashboard page if type is empty', function (): void
{
    Session::put('app-message', [
        'type'     => null,
        'message'  => 'An null message',
        'redirect' => null,
    ]);

    Livewire::actingAs(user: testUser())
        ->test(name: Index::class)
        ->call(method: 'displayMessages')
        ->assertNotDispatched(event: 'toast-show');
});

it('refreshes the dashboard', function (): void
{
    Livewire::actingAs(user: testUser())
        ->test(name: Index::class)
        ->call(method: 'reloadDashboard')
        ->assertDispatched(event: '$refresh');
});
