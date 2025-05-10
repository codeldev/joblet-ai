<?php

/** @noinspection PhpIgnoredClassAliasDeclaration */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Backups\CleanupServiceInterface;
use App\Facades\Gdrive as GdriveFacade;
use App\Services\Backups\CleanupService;
use Illuminate\Support\Facades\Config;

afterEach(closure: function (): void
{
    Mockery::close();
});

test(description: 'it implements the correct interface', closure: function (): void
{
    $service = new CleanupService();

    expect(value: $service)
        ->toBeInstanceOf(class: CleanupServiceInterface::class)
        ->toBeInstanceOf(class: CleanupService::class);
});

test(description: 'it has the required public properties', closure: function (): void
{
    expect(value: $service = new CleanupService)
        ->toHaveProperty(name: 'error')
        ->and($service->error)
        ->toBeNull();
});

test(description: 'it has the required public methods', closure: function (): void
{
    expect(value: method_exists(object_or_class: new CleanupService, method: '__invoke'))
        ->toBeTrue();
});

test(description: 'it has the required method signatures', closure: function (): void
{
    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: CleanupService::class,
        method        : '__invoke'
    );

    expect(value: $reflectionMethod->getReturnType()->getName())
        ->toBe(expected: 'bool');
});

test(description: 'it has the required private methods', closure: function (): void
{
    $reflectionClass = new ReflectionClass(objectOrClass: CleanupService::class);
    $methods         = $reflectionClass->getMethods(filter: ReflectionMethod::IS_PRIVATE);
    $methodNames     = array_map(callback: fn ($method) => $method->getName(), array: $methods);

    expect(value: $methodNames)
        ->toContain(needle: 'attemptCleanup')
        ->toContain(needle: 'clearTrashed')
        ->toContain(needle: 'cleanFiles')
        ->toContain(needle: 'deleteFile')
        ->toContain(needle: 'setErrorResponse');
});

test(description: 'clearTrashed succeeds when all config is present', closure: function (): void
{
    Config::set(key: 'filesystems.disks.google', value: [
        'clientId'     => 'id123',
        'clientSecret' => 'secret123',
        'refreshToken' => 'token123',
    ]);

    $adapterMock = Mockery::mock(class: 'overload:' . Masbug\Flysystem\GoogleDriveAdapter::class);
    $adapterMock->shouldReceive(methodNames: 'emptyTrash')
        ->once()
        ->andReturnTrue();

    $service      = new CleanupService();
    $reflection   = new ReflectionClass(objectOrClass: $service);
    $clearTrashed = $reflection->getMethod(name: 'clearTrashed');
    $clearTrashed->setAccessible(accessible: true);

    $result = $clearTrashed->invoke(object: $service);

    expect(value: $result)
        ->toBeTrue()
        ->and(value: $service->error)
        ->toBeNull();
});

test(description: 'clearTrashed sets error when google refreshToken is missing', closure: function (): void
{
    Config::set(key: 'filesystems.disks.google', value: [
        'clientId'     => 'id123',
        'clientSecret' => 'secret123',
    ]);

    $service      = new CleanupService();
    $reflection   = new ReflectionClass(objectOrClass: $service);
    $clearTrashed = $reflection->getMethod(name: 'clearTrashed');
    $clearTrashed->setAccessible(accessible: true);

    $result = $clearTrashed->invoke(object: $service);

    expect(value: $result)
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

test(description: 'clearTrashed sets error when google clientSecret is missing', closure: function (): void
{
    Config::set(key: 'filesystems.disks.google', value: [
        'clientId' => 'id123',
    ]);

    $service      = new CleanupService();
    $reflection   = new ReflectionClass(objectOrClass: $service);
    $clearTrashed = $reflection->getMethod(name: 'clearTrashed');
    $clearTrashed->setAccessible(accessible: true);

    $result = $clearTrashed->invoke(object: $service);

    expect(value: $result)
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

test(description: 'clearTrashed sets error when google clientId is missing', closure: function (): void
{
    Config::set(key: 'filesystems.disks.google', value: []);

    $service      = new CleanupService();
    $reflection   = new ReflectionClass(objectOrClass: $service);
    $clearTrashed = $reflection->getMethod(name: 'clearTrashed');
    $clearTrashed->setAccessible(accessible: true);

    $result = $clearTrashed->invoke(object: $service);

    expect(value: $result)
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

test(description: 'clearTrashed sets error when google config is missing', closure: function (): void
{
    Config::set(key: 'filesystems.disks.google');

    $service      = new CleanupService();
    $reflection   = new ReflectionClass(objectOrClass: $service);
    $clearTrashed = $reflection->getMethod(name: 'clearTrashed');
    $clearTrashed->setAccessible(accessible: true);

    $result = $clearTrashed->invoke(object: $service);

    expect(value: $result)
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

test(description: 'gives a missing config error when no backup config is found', closure: function (): void
{
    Config::set(key: 'backups');

    $service        = new CleanupService;
    $reflection     = new ReflectionClass(objectOrClass: $service);
    $attemptCleanup = $reflection->getMethod(name: 'attemptCleanup');
    $attemptCleanup->setAccessible(accessible: true);

    expect(value: $attemptCleanup->invoke(object: $service))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: trans(key: 'exceptions.backups.config.invalid'));
});

test(description: 'returns true without processing when files object is empty', closure: function (): void
{
    $diskMock = Mockery::mock();
    $diskMock->shouldReceive(methodNames: 'listContents')
        ->once()
        ->with(Mockery::any(), Mockery::any())
        ->andReturn([]);

    $storageMock = Mockery::mock();
    $storageMock->shouldReceive(methodNames: 'disk')
        ->with('google')
        ->andReturn($diskMock);

    Storage::swap(instance: $storageMock);

    $service        = new CleanupService;
    $reflection     = new ReflectionClass(objectOrClass: $service);
    $attemptCleanup = $reflection->getMethod(name: 'attemptCleanup');
    $attemptCleanup->setAccessible(accessible: true);

    expect(value: $attemptCleanup->invoke(object: $service))
        ->toBeTrue();
});

test(description: 'returns true after processing when files object is not empty', closure: function (): void
{
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive(methodNames: 'listContents')
        ->once()
        ->with(Mockery::any(), Mockery::any())
        ->andReturn([
            (object) [
                'path'      => 'backup1.zip',
                'timestamp' => time(),
                'type'      => 'file',
            ],
            (object) [
                'path'      => 'backup2.zip',
                'timestamp' => time(),
                'type'      => 'file',
            ],
        ]);

    $storageMock = Mockery::mock();
    $storageMock->shouldReceive(methodNames: 'disk')
        ->with('google')
        ->andReturn($mockDisk);

    Storage::swap(instance: $storageMock);

    $service        = new CleanupService;
    $reflection     = new ReflectionClass(objectOrClass: $service);
    $attemptCleanup = $reflection->getMethod(name: 'attemptCleanup');
    $attemptCleanup->setAccessible(accessible: true);

    expect(value: $attemptCleanup->invoke(object: $service))
        ->toBeTrue();
});

test(description: 'it deletes files if more than one file is found', closure: function (): void
{
    GdriveFacade::shouldReceive('all')
        ->zeroOrMoreTimes()
        ->with(Mockery::any())
        ->andReturn(collect(value: getCleanupFiles()));

    GdriveFacade::shouldReceive('delete')
        ->once()
        ->with('backup2.zip')
        ->andReturnTrue();

    $service        = new CleanupService;
    $reflection     = new ReflectionClass(objectOrClass: $service);
    $attemptCleanup = $reflection->getMethod(name: 'attemptCleanup');
    $attemptCleanup->setAccessible(accessible: true);

    expect(value: $attemptCleanup->invoke(object: $service))
        ->toBeTrue();
});

test(description: 'attemptCleanup uses default path if list_from is missing', closure: function (): void
{
    $config = config(key: 'backups');

    unset($config['list_from']);

    Config::set(key: 'backups', value: $config);

    GdriveFacade::shouldReceive('all')
        ->zeroOrMoreTimes()
        ->with('/')
        ->andReturn(collect(value: getCleanupFiles()));

    GdriveFacade::shouldReceive('delete')
        ->once()
        ->with('backup2.zip')
        ->andReturnTrue();

    $service        = new CleanupService;
    $reflection     = new ReflectionClass(objectOrClass: $service);
    $attemptCleanup = $reflection->getMethod(name: 'attemptCleanup');
    $attemptCleanup->setAccessible(accessible: true);

    expect(value: $attemptCleanup->invoke(object: $service))
        ->toBeTrue();
});

test(description: 'runs successfully from invokable method', closure: function (): void
{
    GdriveFacade::shouldReceive('all')
        ->zeroOrMoreTimes()
        ->with(Mockery::any())
        ->andReturn(collect(value: getCleanupFiles()));

    GdriveFacade::shouldReceive('delete')
        ->once()
        ->with('backup2.zip')
        ->andReturnTrue();

    $adapterMock = Mockery::mock(class: 'overload:' . Masbug\Flysystem\GoogleDriveAdapter::class);
    $adapterMock->shouldReceive(methodNames: 'emptyTrash')
        ->once()
        ->andReturnTrue();

    Config::set(key: 'filesystems.disks.google', value: [
        'clientId'     => 'id123',
        'clientSecret' => 'secret123',
        'refreshToken' => 'token123',
    ]);

    $service = new CleanupService;

    expect(value: $service())
        ->toBeTrue();
});

test(description: 'runs with errors from invokable method', closure: function (): void
{
    Config::set(key: 'backups');

    $service = new CleanupService;

    expect(value: $service())
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: trans(key: 'exceptions.backups.config.invalid'));
});

function getCleanupFiles(): array
{
    return [
        new class
        {
            public function path(): string
            {
                return 'backup1.zip';
            }

            public function lastModified(): float | int | string
            {
                return now()->timestamp;
            }

            public function getType(): string
            {
                return 'file';
            }
        },
        new class
        {
            public function path(): string
            {
                return 'backup2.zip';
            }

            public function lastModified(): float | int | string
            {
                return now()->subDays(value: 2)->timestamp;
            }

            public function getType(): string
            {
                return 'file';
            }
        },
    ];
}
