<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Sessions;
use Cjmellor\BrowserSessions\Facades\BrowserSessions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->password = 'password';
    $this->user     = testUser(params: [
        'password' => Hash::make(value: $this->password),
    ]);

    $this->sessionId = Session::getId();

    DB::table(table: 'sessions')->insert(values: [
        'id'            => 'test-session-123',
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Test Agent',
        'payload'       => 'Test Payload',
        'last_activity' => now()->timestamp,
    ]);
});

test(description: 'sessions component can be rendered', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(args: collect());

    Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->assertOk();
});

test(description: 'sessions component displays user sessions', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(args: collect(value: [
            (object) [
                'agent' => [
                    'browser'  => 'Chrome',
                    'platform' => 'Windows',
                ],
                'ip_address'        => '127.0.0.1',
                'is_current_device' => true,
                'last_active'       => now()->subMinutes(value: 5)->diffForHumans(),
                'device'            => [
                    'desktop'  => true,
                    'mobile'   => false,
                    'tablet'   => false,
                    'platform' => 'Windows',
                    'browser'  => 'Chrome',
                ],
            ],
        ]));

    Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->assertSee(values: trans(key: 'account.sessions.device.desktop'));
});

test(description: 'clear sessions action clears other sessions', closure: function (): void
{
    $sessionId = Session::getId();

    DB::table(table: 'sessions')->insert(values: [
        'id'            => $sessionId,
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Current Session',
        'payload'       => 'Current Payload',
        'last_activity' => now()->timestamp,
    ]);

    DB::table(table: 'sessions')->insert(values: [
        'id'            => 'test-session-456',
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.2',
        'user_agent'    => 'Test Agent 2',
        'payload'       => 'Test Payload 2',
        'last_activity' => now()->timestamp,
    ]);

    $initialCount = DB::table(table: 'sessions')
        ->where(column: 'user_id', operator: '=', value: $this->user->getAuthIdentifier())
        ->count();

    expect(value: $initialCount)
        ->toBeGreaterThan(expected: 1);

    BrowserSessions::shouldReceive('sessions')
        ->andReturn(collect());

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->set('form.password', $this->password)
        ->call(method: 'submit');

    $component->assertDispatched(event: 'toast-show');

    $finalCount = DB::table(table: 'sessions')
        ->where(column: 'user_id', operator: '=', value: $this->user->getAuthIdentifier())
        ->count();

    expect(value: $finalCount)
        ->toBeLessThan(expected: $initialCount);
});

test(description: 'form validation works for password field', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(args: collect());

    Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->set('form.password', '')
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password' => 'required']);

    Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->set('form.password', 'wrong-password')
        ->call(method: 'submit')
        ->assertHasErrors(keys: ['form.password' => 'current_password']);
});

test(description: 'cancel method clears the form', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(args: collect());

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class)
        ->set('form.password', 'some-password')
        ->call(method: 'cancel');

    expect(value: $component->get('form.password'))
        ->toBe(expected: '');
});

test(description: 'device type is correctly determined for desktop', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(collect(value: [
            (object) [
                'device' => [
                    'desktop'  => true,
                    'mobile'   => false,
                    'tablet'   => false,
                    'platform' => 'Windows',
                    'browser'  => 'Chrome',
                ],
                'agent' => [
                    'browser'  => 'Chrome',
                    'platform' => 'Windows',
                ],
                'ip_address'        => '127.0.0.1',
                'is_current_device' => true,
                'last_active'       => now()->subMinutes(value: 5)->diffForHumans(),
            ],
        ]));

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class);

    $sessions = $component->instance()->sessions;

    expect(value: $sessions->first()->deviceType)
        ->toBe(expected: trans(key: 'account.sessions.device.desktop'));
});

test(description: 'device type is correctly determined for mobile', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(collect(value: [
            (object) [
                'device' => [
                    'desktop'  => false,
                    'mobile'   => true,
                    'tablet'   => false,
                    'platform' => 'iOS',
                    'browser'  => 'Safari',
                ],
                'agent' => [
                    'browser'  => 'Safari',
                    'platform' => 'iOS',
                ],
                'ip_address'        => '127.0.0.1',
                'is_current_device' => true,
                'last_active'       => now()->subMinutes(value: 5)->diffForHumans(),
            ],
        ]));

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class);

    $sessions = $component->instance()->sessions;

    expect(value: $sessions->first()->deviceType)
        ->toBe(expected: trans(key: 'account.sessions.device.mobile'));
});

test(description: 'device type is correctly determined for tablet', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(collect(value: [
            (object) [
                'device' => [
                    'desktop'  => false,
                    'mobile'   => false,
                    'tablet'   => true,
                    'platform' => 'iPadOS',
                    'browser'  => 'Safari',
                ],
                'agent' => [
                    'browser'  => 'Safari',
                    'platform' => 'iPadOS',
                ],
                'ip_address'        => '127.0.0.1',
                'is_current_device' => true,
                'last_active'       => now()->subMinutes(value: 5)->diffForHumans(),
            ],
        ]));

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class);

    $sessions = $component->instance()->sessions;

    expect(value: $sessions->first()->deviceType)
        ->toBe(expected: trans(key: 'account.sessions.device.tablet'));
});

test(description: 'device type defaults to unknown when no device type is matched', closure: function (): void
{
    BrowserSessions::shouldReceive('sessions')
        ->andReturn(collect(value: [
            (object) [
                'device' => [
                    'desktop'  => false,
                    'mobile'   => false,
                    'tablet'   => false,
                    'platform' => 'Unknown',
                    'browser'  => 'Unknown',
                ],
                'agent' => [
                    'browser'  => 'Unknown',
                    'platform' => 'Unknown',
                ],
                'ip_address'        => '127.0.0.1',
                'is_current_device' => true,
                'last_active'       => now()->subMinutes(value: 5)->diffForHumans(),
            ],
        ]));

    $component = Livewire::actingAs(user: $this->user)
        ->test(name: Sessions::class);

    $sessions = $component->instance()->sessions;

    expect(value: $sessions->first()->deviceType)
        ->toBe(expected: trans(key: 'account.sessions.device.unknown'));
});
