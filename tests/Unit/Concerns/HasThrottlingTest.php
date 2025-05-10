<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mockery;
use Tests\Classes\Unit\Concerns\HasThrottlingTest;
use Tests\Classes\Unit\Concerns\TestForm;

beforeEach(closure: function (): void
{
    $this->throttleable = new HasThrottlingTest;
    $this->request      = Mockery::mock(args: Request::class);
    $this->request->shouldReceive(methodNames: 'root')->andReturn('http://localhost');
    $this->request->shouldReceive(methodNames: 'ip')->andReturn('127.0.0.1');
    $this->request->shouldReceive(methodNames: 'setUserResolver')->andReturnSelf();

    app()->instance(abstract: 'request', instance: $this->request);

    $this->throttleable->clearRateLimits();
});

afterEach(closure: function (): void
{
    Mockery::close();
});

test(description: 'setupProperties sets the correct properties', closure: function (): void
{
    $this->throttleable->mount();

    expect(value: $this->throttleable->keyPrefix)
        ->toBe(expected: 'test')
        ->and(value: $this->throttleable->lockoutRedirect)
        ->toBe(expected: 'auth');
});

test(description: 'checkLockedOutOnMount sets lockout message when locked out', closure: function (): void
{
    $this->throttleable->mount();

    for ($i = 1; $i <= 5; $i++)
    {
        $this->throttleable->addLimiterHit();
    }

    $this->throttleable->checkLockedOutOnMount();

    expect(value: $this->throttleable->lockoutMessage)
        ->not->toBeNull();
});

test(description: 'checkLockedOutOnMount does nothing when not locked out', closure: function (): void
{
    $this->throttleable->mount();

    $this->throttleable->checkLockedOutOnMount();

    expect(value: $this->throttleable->lockoutMessage)
        ->toBeNull();
});

test(description: 'ensureIsNotRateLimited fires lockout event and redirects when locked out', closure: function (): void
{
    Event::fake();

    $this->throttleable->mount();

    for ($i = 1; $i <= 5; $i++)
    {
        $this->throttleable->addLimiterHit();
    }

    $this->throttleable->ensureIsNotRateLimited();

    Event::assertDispatched(event: Lockout::class);
});

test(description: 'ensureIsNotRateLimited does nothing when not locked out', closure: function (): void
{
    Event::fake();

    $this->throttleable->mount();

    for ($i = 1; $i <= 2; $i++)
    {
        $this->throttleable->addLimiterHit();
    }

    $this->throttleable->ensureIsNotRateLimited();

    Event::assertNotDispatched(event: Lockout::class);
});

test(description: 'throttleKeys returns expected keys', closure: function (): void
{
    $this->throttleable->mount();

    $sessionId = 'test_session_id';
    $emailHash = mb_substr(md5(string: Str::lower(value: $this->throttleable->form->email)), 0, 8);

    Session::shouldReceive('getId')->andReturn($sessionId);

    $keys = $this->throttleable->throttleKeys();

    expect(value: $keys)
        ->toBeArray()
        ->and(value: $keys)
        ->toHaveCount(count: 2)
        ->and(value: $keys[0])
        ->toBe(expected: Str::transliterate(string: "test|127.0.0.1|{$sessionId}"))
        ->and(value: $keys[1])
        ->toBe(expected: Str::transliterate(string: "test|127.0.0.1|{$emailHash}"));
});

test(description: 'throttleKeys uses session ID when no email is available', closure: function (): void
{
    $this->throttleable->mount();

    $this->throttleable->form = new TestForm();

    $sessionId = 'test_session_id';
    $emailHash = mb_substr(md5(string: Str::lower(value: $sessionId)), 0, 8);

    Session::shouldReceive('getId')->andReturn($sessionId);

    $keys = $this->throttleable->throttleKeys();

    expect(value: $keys)
        ->toBeArray()
        ->and(value: $keys)
        ->toHaveCount(count: 2)
        ->and(value: $keys[0])
        ->toBe(expected: Str::transliterate(string: "test|127.0.0.1|{$sessionId}"))
        ->and(value: $keys[1])
        ->toBe(expected: Str::transliterate(string: "test|127.0.0.1|{$emailHash}"));
});

test(description: 'throttleKeys works with null prefix', closure: function (): void
{
    $this->throttleable->mount();

    $sessionId = 'test_session_id';
    Session::shouldReceive('getId')->andReturn($sessionId);

    $this->throttleable->keyPrefix = null;

    $keys = $this->throttleable->throttleKeys();

    expect(value: $keys[0])
        ->toBe(expected: Str::transliterate(string: "|127.0.0.1|{$sessionId}"));
});

test(description: 'addLimiterHit adds hits to all throttle keys', closure: function (): void
{
    $this->throttleable->mount();

    Session::shouldReceive('getId')->andReturn('test_session_id');

    for ($i = 1; $i <= 2; $i++)
    {
        $this->throttleable->addLimiterHit();
    }

    $keys = $this->throttleable->throttleKeys();

    expect(value: RateLimiter::attempts($keys[0]))
        ->toEqual(expected: 2)
        ->and(RateLimiter::attempts($keys[0]))
        ->toEqual(expected: 2);
});

test(description: 'ensure decay time returns a number', closure: function (): void
{
    $decayTime = $this->throttleable->getDecayTime(decayTime: 60);

    expect(value: $decayTime)
        ->toBeInt()
        ->and(value: $decayTime)
        ->toEqual(expected: 60);
});
