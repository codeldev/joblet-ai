<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Console\Commands\Backups\CleanBackupsCommand;
use App\Contracts\Services\Backups\CleanupServiceInterface;
use App\Models\User;
use App\Notifications\Backups\CleanupFailedNotification;

use function Pest\Laravel\artisan;

use Tests\Classes\Console\CleanupServiceErrorStub;
use Tests\Classes\Console\CleanupServiceSuccessStub;
use Tests\Classes\Console\CleanupServiceUnknownErrorStub;

it(description: 'returns success and outputs translated message when cleanup is successful', closure: function (): void
{
    app()->bind(
        abstract: CleanupServiceInterface::class,
        concrete: fn () => new CleanupServiceSuccessStub
    );

    $result = artisan(command: 'backup:clean');
    $result->assertExitCode(exitCode: CleanBackupsCommand::SUCCESS);
    $result->expectsOutputToContain(string: trans(key: 'backups.cleanup.success'));
});

it(description: 'returns failure and outputs translated error when cleanup fails with error', closure: function (): void
{
    app()->bind(
        abstract: CleanupServiceInterface::class,
        concrete: fn () => new CleanupServiceErrorStub
    );

    $result = artisan(command: 'backup:clean');
    $result->assertExitCode(exitCode: CleanBackupsCommand::FAILURE);
    $result->expectsOutputToContain(string: 'Custom cleanup error');
});

it(description: 'returns failure and outputs translated unknown error when cleanup fails without error', closure: function (): void
{
    app()->bind(
        abstract: CleanupServiceInterface::class,
        concrete: fn () => new CleanupServiceUnknownErrorStub
    );

    $result = artisan(command: 'backup:clean');
    $result->assertExitCode(exitCode: CleanBackupsCommand::FAILURE);
    $result->expectsOutputToContain(string: trans(key: 'backups.unknown.error'));
});

it('sends notification when sendNotification method is called', function (): void
{
    Notification::fake();

    $sendToEmail  = 'test@example.com';
    $errorMessage = 'Test error message';

    Config::set('settings.contact', $sendToEmail);

    $command    = new CleanBackupsCommand;
    $reflection = new ReflectionMethod(objectOrMethod: $command, method: 'sendNotification');
    $reflection->setAccessible(accessible: true);
    $reflection->invoke($command, $errorMessage);

    Notification::assertSentTo(
        notifiable  : new User(attributes: ['email' => $sendToEmail]),
        notification: CleanupFailedNotification::class,
        callback    : fn ($notification, $channels) => $notification->errorMessage === $errorMessage
    );
});
