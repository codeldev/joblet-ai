<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\System\SendExceptionMailAction;
use App\Notifications\System\ExceptionReportNotification;
use App\Services\Models\UserService;

describe(description: 'Send Exception Mail Action', tests: function (): void
{
    it(description: 'builds exception data correctly', closure: function (): void
    {
        $exception  = new Exception(message: 'Test exception message');
        $action     = new SendExceptionMailAction(exception: $exception);
        $reflection = new ReflectionClass(objectOrClass: $action);
        $method     = $reflection->getMethod(name: 'buildExceptionData');
        $method->setAccessible(accessible: true);

        $result = $method->invoke(object: $action);

        expect(value: $result)
            ->toBeArray()
            ->toHaveKeys(keys: ['message', 'file', 'line', 'trace', 'url', 'body', 'ip', 'user'])
            ->and(value: $result['message'])
            ->toBe(expected: 'Test exception message')
            ->and(value: $result['file'])
            ->toBe(expected: $exception->getFile())
            ->and(value: $result['line'])
            ->toBe(expected: $exception->getLine());
    });

    it(description: 'formats exception trace correctly', closure: function (): void
    {
        $exception  = new Exception(message: 'Test exception with trace');
        $action     = new SendExceptionMailAction(exception: $exception);
        $reflection = new ReflectionClass(objectOrClass: $action);
        $method     = $reflection->getMethod(name: 'buildExceptionData');
        $method->setAccessible(accessible: true);

        $result = $method->invoke(object: $action);

        expect(value: $result['trace'])
            ->toBeArray()
            ->not->toBeEmpty();

        if (! empty($result['trace']))
        {
            expect(value: array_key_exists('args', $result['trace'][0]))
                ->toBeFalse();
        }
    });

    it(description: 'sends notification', closure: function (): void
    {
        Notification::fake();

        Config::set('settings.exceptions.send_mail', true);

        new SendExceptionMailAction(
            exception: new Exception(message: 'Test exception with trace')
        )->handle();

        Notification::assertSentTo(
            notifiable: UserService::getSupportUser(),
            notification: ExceptionReportNotification::class,
        );
    });

    it(description: 'does not send notification when setting is disabled', closure: function (): void
    {
        Notification::fake();

        Config::set('settings.exceptions.send_mail', false);

        new SendExceptionMailAction(
            exception: new Exception(message: 'Test exception with trace')
        )->handle();

        Notification::assertNothingSent();
    });

    it(description: 'does not send notification on local environments', closure: function (): void
    {
        Notification::fake();

        Config::set('app.env', 'local');
        Config::set('database.default', 'sqlite');
        Config::set('settings.exceptions.send_mail', true);

        app()->detectEnvironment(callback: fn () => 'local');

        new SendExceptionMailAction(
            exception: new Exception(message: 'Test exception with trace')
        )->handle();

        Notification::assertNothingSent();
    });
});
