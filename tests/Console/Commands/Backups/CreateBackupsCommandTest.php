<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Console\Commands\Backups\CreateBackupCommand;
use App\Contracts\Services\Backups\BackupServiceInterface;
use App\Models\User;
use App\Notifications\Backups\BackupFailedNotification;

use function Pest\Laravel\artisan;

use Tests\Classes\Console\BackupServiceErrorStub;
use Tests\Classes\Console\BackupServiceSuccessStub;
use Tests\Classes\Console\BackupServiceUnknownErrorStub;

it(description: 'returns success and outputs translated message when backup is created', closure: function (): void
{
    app()->bind(
        abstract: BackupServiceInterface::class,
        concrete: fn () => new BackupServiceSuccessStub
    );

    $result = artisan(command: 'backup:create');
    $result->assertExitCode(exitCode: CreateBackupCommand::SUCCESS);
    $result->expectsOutputToContain(string: trans(key: 'backups.created.success'));
});

it(description: 'returns failure and outputs translated error when backup fails with error', closure: function (): void
{
    app()->bind(
        abstract: BackupServiceInterface::class,
        concrete: fn () => new BackupServiceErrorStub
    );

    $result = artisan(command: 'backup:create');
    $result->assertExitCode(exitCode: CreateBackupCommand::FAILURE);
    $result->expectsOutputToContain(string: 'Custom error message');
});

it(description: 'returns failure and outputs translated unknown error when backup fails without error', closure: function (): void
{
    app()->bind(
        abstract: BackupServiceInterface::class,
        concrete: fn () => new BackupServiceUnknownErrorStub
    );

    $result = artisan(command: 'backup:create');
    $result->assertExitCode(exitCode: CreateBackupCommand::FAILURE);
    $result->expectsOutputToContain(string: trans(key: 'backups.unknown.error'));
});

it('sends notification when sendNotification method is called', function (): void
{
    Notification::fake();

    $sendToEmail  = 'test@example.com';
    $errorMessage = 'Test error message';

    Config::set('settings.contact', $sendToEmail);

    $command    = new CreateBackupCommand;
    $reflection = new ReflectionMethod(objectOrMethod: $command, method: 'sendNotification');
    $reflection->setAccessible(accessible: true);
    $reflection->invoke($command, $errorMessage);

    Notification::assertSentTo(
        notifiable  : new User(attributes: ['email' => $sendToEmail]),
        notification: BackupFailedNotification::class,
        callback    : fn ($notification, $channels) => $notification->errorMessage === $errorMessage
    );
});
