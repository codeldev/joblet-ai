<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUndefinedMethodInspection */

declare(strict_types=1);

use App\Actions\Account\ClearSessionAction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

beforeEach(closure: function (): void
{
    $this->password = 'password';
    $this->user     = testUser(params: [
        'password' => Hash::make(value: $this->password),
    ]);

    $this->actingAs(user: $this->user);
});

test(description: 'handle method processes invalid password correctly', closure: function (): void
{
    $sessionId       = 'test-session-id';
    $guardName       = 'web';
    $invalidPassword = 'invalid-password';

    config()->set(key: 'browser-sessions.browser_session_guard', value: $guardName);

    $mockGuard = Mockery::mock(args: StatefulGuard::class);
    $mockGuard->shouldReceive(methodNames: 'logoutOtherDevices')
        ->once()
        ->withArgs(fn ($password) => $password === $invalidPassword)
        ->andThrow(exception: new AuthenticationException(message: 'Invalid credentials'));

    Auth::shouldReceive('guard')
        ->once()
        ->with($guardName)
        ->andReturn(args: $mockGuard);

    Auth::shouldReceive('user')
        ->once()
        ->andReturn(args: $this->user);

    Session::shouldReceive('getId')
        ->once()
        ->andReturn(args: $sessionId);

    DB::table(table: 'sessions')->insert(values: [
        'id'            => $sessionId,
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Test Agent',
        'payload'       => 'Test Payload',
        'last_activity' => now()->timestamp,
    ]);

    (new ClearSessionAction)->handle(validated: [
        'password' => $invalidPassword,
    ]);

    $sessionExists = DB::table(table: 'sessions')
        ->where(column: 'id', operator: '=', value: $sessionId)
        ->exists();

    expect(value: $sessionExists)
        ->toBeTrue();
});

test(description: 'deleteOtherSessionRecords method deletes other sessions', closure: function (): void
{
    DB::table(table: 'sessions')->insert(values: [
        [
            'id'            => 'test-session-1',
            'user_id'       => $this->user->getAuthIdentifier(),
            'ip_address'    => '127.0.0.1',
            'user_agent'    => 'Test Agent 1',
            'payload'       => 'Test Payload 1',
            'last_activity' => now()->timestamp,
        ],
        [
            'id'            => 'test-session-2',
            'user_id'       => $this->user->getAuthIdentifier(),
            'ip_address'    => '127.0.0.2',
            'user_agent'    => 'Test Agent 2',
            'payload'       => 'Test Payload 2',
            'last_activity' => now()->timestamp,
        ],
    ]);

    $currentSessionId = 'current-session-id';

    Session::shouldReceive('getId')
        ->andReturn(args: $currentSessionId);

    DB::table(table: 'sessions')->insert(values: [
        'id'            => $currentSessionId,
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.3',
        'user_agent'    => 'Current Agent',
        'payload'       => 'Current Payload',
        'last_activity' => now()->timestamp,
    ]);

    $action     = new ClearSessionAction();
    $reflection = new ReflectionClass(objectOrClass: $action);
    $method     = $reflection->getMethod(name: 'deleteOtherSessionRecords');

    $method->setAccessible(accessible: true);
    $method->invoke(object: $action);

    $testSession1Exists = DB::table(table: 'sessions')
        ->where(column: 'id', operator: '=', value: 'test-session-1')
        ->exists();

    $testSession2Exists = DB::table(table: 'sessions')
        ->where(column: 'id', operator: '=', value: 'test-session-2')
        ->exists();

    $currentSessionExists = DB::table(table: 'sessions')
        ->where(column: 'id', operator: '=', value: $currentSessionId)
        ->exists();

    expect(value: $testSession1Exists)
        ->toBeFalse()
        ->and(value: $testSession2Exists)
        ->toBeFalse()
        ->and(value: $currentSessionExists)
        ->toBeTrue();
});

test(description: 'clear session action does nothing when user is not authenticated', closure: function (): void
{
    DB::table(table: 'sessions')->insert(values: [
        'id'            => 'test-session-1',
        'user_id'       => $this->user->getAuthIdentifier(),
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Test Agent 1',
        'payload'       => 'Test Payload 1',
        'last_activity' => now()->timestamp,
    ]);

    Auth::logout();

    $initialCount = DB::table(table: 'sessions')
        ->where(column: 'user_id', operator: '=', value: $this->user->getAuthIdentifier())
        ->count();

    $action     = new ClearSessionAction();
    $reflection = new ReflectionClass(objectOrClass: $action);
    $method     = $reflection->getMethod(name: 'deleteOtherSessionRecords');

    $method->setAccessible(accessible: true);
    $method->invoke(object: $action);

    $finalCount = DB::table(table: 'sessions')
        ->where(column: 'user_id', operator: '=', value: $this->user->getAuthIdentifier())
        ->count();

    expect(value: $finalCount)
        ->toBe(expected: $initialCount);
});

test(description: 'signoutOtherDevices method returns self for method chaining', closure: function (): void
{
    $action     = new ClearSessionAction();
    $reflection = new ReflectionClass(objectOrClass: $action);
    $method     = $reflection->getMethod(name: 'signoutOtherDevices');

    $method->setAccessible(accessible: true);

    $result = $method->invoke($action, $this->password);

    expect(value: $result)
        ->toBeInstanceOf(class: ClearSessionAction::class);
});

test(description: 'signoutOtherDevices method handles AuthenticationException and continues execution', closure: function (): void
{
    $mockGuard = Mockery::mock(args: SessionGuard::class);
    $mockGuard->shouldReceive(methodNames: 'logoutOtherDevices')
        ->once()
        ->with(Mockery::type(expected: 'string'))
        ->andThrow(exception: new AuthenticationException(message: 'Invalid credentials'));

    config()->set(key: 'browser-sessions.browser_session_guard', value: 'web');

    Auth::shouldReceive('guard')
        ->once()
        ->with('web')
        ->andReturn($mockGuard);

    $action     = new ClearSessionAction();
    $reflection = new ReflectionClass(objectOrClass: $action);
    $method     = $reflection->getMethod(name: 'signoutOtherDevices');
    $method->setAccessible(accessible: true);

    $result = $method->invoke($action, 'wrong-password');

    expect(value: $result)
        ->toBeInstanceOf(class: ClearSessionAction::class);
});
