<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

use App\Contracts\Services\Backups\BackupServiceInterface;
use App\Facades\Gdrive;
use App\Facades\MySqlDumper;
use App\Services\Backups\BackupService;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\Classes\Unit\Services\Backups\TestBackupServiceFileNameWrapper;
use Tests\Classes\Unit\Services\Backups\TestBackupServiceImplementation;
use Tests\Classes\Unit\Services\Backups\TestBackupServiceInjectionRunner;
use Tests\Classes\Unit\Services\Backups\TestBackupServiceMockWrapper;
use Tests\Classes\Unit\Services\Backups\TestBackupServiceRunner;
use Tests\Classes\Unit\Services\Backups\TestCustomBackupServiceImplementation;
use ZanySoft\Zip\Facades\Zip;

test(description: 'it implements the correct interface', closure: function (): void
{
    expect(value: new BackupService)
        ->toBeInstanceOf(class: BackupServiceInterface::class);
});

test(description: 'it has the required public properties', closure: function (): void
{
    expect(value: $service = new BackupService)
        ->toHaveProperty(name: 'error')
        ->and(value: $service->error)
        ->toBeNull();
});

test(description: 'it has the required public methods', closure: function (): void
{
    expect(value: method_exists(object_or_class: new BackupService, method: '__invoke'))
        ->toBeTrue();
});

test(description: 'it has the required method signatures', closure: function (): void
{
    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: BackupService::class,
        method        : '__invoke'
    );

    expect(value: $reflectionMethod->getReturnType()
        ->getName())
        ->toBe(expected: 'bool');
});

test(description: 'it has a constructor that sets up the service', closure: function (): void
{
    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: BackupService::class,
        method        : '__construct'
    );

    expect(value: $reflectionMethod->getNumberOfParameters())
        ->toBe(expected: 0);
});

test(description: '__invoke returns false if error is set', closure: function (): void
{
    $service        = new BackupService;
    $service->error = 'Some error';

    expect(value: $service())
        ->toBeFalse();
});

test(description: 'BackupServiceInterface can be mocked for contract', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: '__invoke')
        ->andReturn(false);

    expect(value: $mock->__invoke())
        ->toBeFalse();
});

test(description: 'BackupServiceInterface can be mocked to return true', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: '__invoke')
        ->andReturn(true);

    expect(value: $mock->__invoke())
        ->toBeTrue();
});

test(description: 'BackupServiceInterface can be injected into a class', closure: function (): void
{
    $testClass = new class(Mockery::mock(class: BackupServiceInterface::class))
    {
        public function __construct(BackupServiceInterface $backupService) {}
    };

    expect(value: $testClass)->toBeObject();
});

test(description: 'mocked BackupServiceInterface works correctly when injected', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: '__invoke')
        ->once()
        ->andReturn(true);

    expect(value: new TestBackupServiceInjectionRunner(backupService: $mock)->runBackup())
        ->toBeTrue();
});

test(description: 'interface can be bound in Laravel container', closure: function (): void
{
    $container = new Container;
    $container->bind(abstract: BackupServiceInterface::class, concrete: BackupService::class);

    expect(value: $container->make(abstract: BackupServiceInterface::class))
        ->toBeInstanceOf(class: BackupService::class);
});

test(description: 'interface can be bound as singleton in Laravel container', closure: function (): void
{
    $container = new Container;
    $container->singleton(abstract: BackupServiceInterface::class, concrete: BackupService::class);

    expect(value: $container->make(abstract: BackupServiceInterface::class))
        ->toBe(expected: $container->make(abstract: BackupServiceInterface::class));
});

test(description: 'mock can be bound to interface in Laravel container', closure: function (): void
{
    $container = new Container();
    $mock      = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: '__invoke')
        ->once()
        ->andReturn(true);

    $container->instance(abstract: BackupServiceInterface::class, instance: $mock);
    $service = $container->make(abstract: BackupServiceInterface::class);

    expect(value: $service)->toBe(expected: $mock)
        ->and(value: $service->__invoke())->toBeTrue();
});

test(description: 'setErrorResponse sets error property and returns false', closure: function (): void
{
    $service   = new BackupService();
    $exception = new Exception(message: 'Test error message');

    $reflection = new ReflectionClass(objectOrClass: BackupService::class);
    $method     = $reflection->getMethod(name: 'setErrorResponse');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke($service, $exception))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: 'Test error message');
});

test(description: 'setErrorResponse sets error message from exception', closure: function (): void
{
    $exception = new Exception(message: 'Custom test error message');
    $service   = app(abstract: BackupServiceInterface::class);

    $reflection = new ReflectionClass(objectOrClass: get_class(object: $service));
    $method     = $reflection->getMethod(name: 'setErrorResponse');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke($service, $exception))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: 'Custom test error message');
});

test(description: 'setErrorResponse calls cleanup after setting error', closure: function (): void
{
    $service         = app(abstract: BackupServiceInterface::class);
    $exception       = new Exception(message: 'Test error message');
    $reflectionClass = new ReflectionClass(objectOrClass: get_class(object: $service));

    $cleanupMethod = $reflectionClass->getMethod(name: 'cleanup');
    $cleanupMethod->setAccessible(accessible: true);

    $setErrorMethod = $reflectionClass->getMethod(name: 'setErrorResponse');
    $setErrorMethod->setAccessible(accessible: true);

    expect(value: $setErrorMethod->invoke($service, $exception))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: 'Test error message');
});

test(description: 'createDatabaseDump returns false and sets error on exception', closure: function (): void
{
    MySqlDumper::shouldReceive('create')
        ->andThrow(exception: new Exception(message: 'Database dump failed'));

    $service    = new BackupService();
    $reflection = new ReflectionClass(objectOrClass: BackupService::class);

    $sqlPath = $reflection->getProperty(name: 'sqlPath');
    $sqlPath->setAccessible(accessible: true);
    $sqlPath->setValue(objectOrValue: $service, value: '/tmp/database.sql');

    $dbConfig = $reflection->getProperty(name: 'dbConfig');
    $dbConfig->setAccessible(accessible: true);
    $dbConfig->setValue(objectOrValue: $service, value: [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        'database' => 'test_db',
        'username' => 'test_user',
        'password' => 'test_password',
    ]);

    $method = $reflection->getMethod(name: 'createDatabaseDump');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke(object: $service))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: 'Database dump failed');
});

test(description: 'createBackupFile returns false and sets error on exception', closure: function (): void
{
    Zip::shouldReceive('create')
        ->andThrow(exception: new Exception(message: 'Zip creation failed'));

    $service = new BackupService();

    $reflectionClass = new ReflectionClass(objectOrClass: BackupService::class);

    $filePath = $reflectionClass->getProperty(name: 'filePath');
    $filePath->setAccessible(accessible: true);
    $filePath->setValue(objectOrValue: $service, value: '/tmp/test.zip');

    $envPath = $reflectionClass->getProperty(name: 'envPath');
    $envPath->setAccessible(accessible: true);
    $envPath->setValue(objectOrValue: $service, value: '/tmp/.env');

    $sqlPath = $reflectionClass->getProperty(name: 'sqlPath');
    $sqlPath->setAccessible(accessible: true);
    $sqlPath->setValue(objectOrValue: $service, value: '/tmp/database.sql');

    $method = $reflectionClass->getMethod(name: 'createBackupFile');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke(object: $service))
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: 'Zip creation failed');
});

test(description: '__invoke handles errors from all steps', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: '__invoke')
        ->andReturn(false);

    expect(value: $mock->__invoke())
        ->toBeFalse();

    $service        = new BackupService();
    $service->error = 'Pre-existing error';

    expect(value: $service->__invoke())
        ->toBeFalse();
});

test(description: 'interface can be used in a service container', closure: function (): void
{
    $container = new Container();

    $container->singleton(abstract: BackupServiceInterface::class, concrete: fn () => tap(
        value   : Mockery::mock(class: BackupServiceInterface::class),
        callback: fn ($mock) => $mock->shouldReceive(methodNames: '__invoke')
            ->andReturn(true)
    ));

    expect(value: $service = $container->make(abstract: BackupServiceInterface::class))
        ->toBeInstanceOf(class: Mockery\MockInterface::class)
        ->and(value: $service->__invoke())
        ->toBeTrue();
});

test(description: 'interface contract can be used with a custom implementation', closure: function (): void
{
    expect(value: $customImplementation = new TestCustomBackupServiceImplementation)
        ->toBeInstanceOf(class: BackupServiceInterface::class)
        ->and(value: $customImplementation->__invoke())
        ->toBeTrue()
        ->and(value: new TestBackupServiceRunner(backupService: $customImplementation)->runBackup())
        ->toBeTrue();
});

test(description: 'cleanup method deletes temporary files', closure: function (): void
{
    $service    = new BackupService();
    $reflection = new ReflectionClass(objectOrClass: BackupService::class);

    $fileName = $reflection->getProperty(name: 'fileName');
    $fileName->setAccessible(accessible: true);
    $fileName->setValue(objectOrValue: $service, value: 'test.zip');

    $sqlName = $reflection->getProperty(name: 'sqlName');
    $sqlName->setAccessible(accessible: true);
    $sqlName->setValue(objectOrValue: $service, value: 'database.sql');

    $disk = $reflection->getProperty(name: 'disk');
    $disk->setAccessible(accessible: true);

    $mockFilesystem = Mockery::mock(class: Filesystem::class);

    $mockFilesystem->shouldReceive(methodNames: 'delete')
        ->with('test.zip')
        ->once()
        ->andReturn(true);

    $mockFilesystem->shouldReceive(methodNames: 'delete')
        ->with('database.sql')
        ->once()
        ->andReturn(true);

    $disk->setValue(objectOrValue: $service, value: $mockFilesystem);

    $method = $reflection->getMethod(name: 'cleanup');
    $method->setAccessible(accessible: true);

    $method->invoke(object: $service);
});

test(description: 'backup service can be configured with a custom filename', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: 'setFileName')
        ->once()
        ->with('custom-backup.zip')
        ->andReturn(Mockery::self());

    $testClass = new TestBackupServiceFileNameWrapper(backupService: $mock);

    expect(value: $testClass->getBackupService())
        ->toBe(expected: $mock);

    $realService   = new BackupService();
    $realTestClass = new TestBackupServiceFileNameWrapper(backupService: $realService);

    expect(value: $realTestClass->getBackupService())
        ->toBe(expected: $realService)
        ->and(value: $realService->error)
        ->toBeNull();
});

test(description: 'backup service can be configured with a custom SQL filename', closure: function (): void
{
    $mock = Mockery::mock(class: BackupServiceInterface::class);
    $mock->shouldReceive(methodNames: 'setSqlName')
        ->once()
        ->with('custom-database.sql')
        ->andReturn(Mockery::self());

    $testClass = new TestBackupServiceMockWrapper(backupService: $mock);

    expect(value: $testClass->getBackupService())
        ->toBe(expected: $mock);

    $realService   = new BackupService();
    $realTestClass = new TestBackupServiceMockWrapper(backupService: $realService);

    expect(value: $realTestClass->getBackupService())
        ->toBe(expected: $realService)
        ->and(value: $realService->error)
        ->toBeNull();

    $reflection = new ReflectionClass(objectOrClass: BackupService::class);
    $sqlName    = $reflection->getProperty(name: 'sqlName');
    $sqlName->setAccessible(accessible: true);

    expect(value: $sqlName->getValue(object: $realService))
        ->toBe(expected: 'custom-database.sql');
});

test(description: 'setDisk method sets the disk property correctly', closure: function (): void
{
    Storage::shouldReceive('disk')
        ->with('local')
        ->andReturn($mockDisk = Mockery::mock(class: Filesystem::class));

    $mockDisk->shouldReceive(methodNames: 'delete')
        ->withAnyArgs()
        ->andReturn(true);

    $mockDisk->shouldReceive(methodNames: 'path')
        ->withAnyArgs()
        ->andReturn('/tmp/test/path');

    $service         = new BackupService();
    $reflectionClass = new ReflectionClass(objectOrClass: BackupService::class);
    $setDiskMethod   = $reflectionClass->getMethod(name: 'setDisk');
    $setDiskMethod->setAccessible(accessible: true);

    expect(value: $setDiskMethod->invoke($service))
        ->toBe(expected: $service);

    $diskProperty = $reflectionClass->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);

    expect(value: $diskProperty->getValue(object: $service))
        ->toBe(expected: $mockDisk);
});

test(description: 'setEnvPath method sets the envPath property correctly', closure: function (): void
{
    Storage::shouldReceive('disk')
        ->with('local')
        ->andReturn($mockDisk = Mockery::mock(class: Filesystem::class));

    $mockDisk->shouldReceive(methodNames: 'delete')
        ->withAnyArgs()
        ->andReturn(true);

    $mockDisk->shouldReceive(methodNames: 'path')
        ->withAnyArgs()
        ->andReturnUsing(fn (string $path) => '/tmp/test/' . $path);

    $service      = new BackupService();
    $reflection   = new ReflectionClass(objectOrClass: BackupService::class);
    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $mockDisk);

    $setEnvPathMethod = $reflection->getMethod(name: 'setEnvPath');
    $setEnvPathMethod->setAccessible(accessible: true);

    expect(value: $setEnvPathMethod->invoke(object: $service))
        ->toBe(expected: $service);

    $envPathProperty = $reflection->getProperty(name: 'envPath');
    $envPathProperty->setAccessible(accessible: true);

    expect(value: $envPathProperty->getValue(object: $service))
        ->toContain(needle: '.env');
});

test(description: 'setSqlPath method sets the sqlPath property correctly', closure: function (): void
{
    Storage::shouldReceive('disk')
        ->with('local')
        ->andReturn($mockDisk = Mockery::mock(class: Filesystem::class));

    $mockDisk->shouldReceive(methodNames: 'delete')
        ->withAnyArgs()
        ->andReturn(true);

    $mockDisk->shouldReceive(methodNames: 'path')
        ->withAnyArgs()
        ->andReturnUsing(fn (string $path) => '/tmp/test/' . $path);

    $service         = new BackupService();
    $reflectionClass = new ReflectionClass(objectOrClass: BackupService::class);
    $diskProperty    = $reflectionClass->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $mockDisk);

    $setSqlPathMethod = $reflectionClass->getMethod(name: 'setSqlPath');
    $setSqlPathMethod->setAccessible(accessible: true);

    expect(value: $setSqlPathMethod->invoke(object: $service))
        ->toBe(expected: $service);

    $sqlNameProperty = $reflectionClass->getProperty(name: 'sqlName');
    $sqlNameProperty->setAccessible(accessible: true);

    expect(value: $sqlNameProperty->getValue(object: $service))
        ->toBe(expected: 'database.sql');

    $sqlPathProperty = $reflectionClass->getProperty(name: 'sqlPath');
    $sqlPathProperty->setAccessible(accessible: true);

    expect(value: $sqlPathProperty->getValue(object: $service))
        ->toContain(needle: 'database.sql');
});

test(description: 'setDbConfig method sets the dbConfig property correctly', closure: function (): void
{
    $reflectionClass  = new ReflectionClass(objectOrClass: BackupService::class);
    $dbConfigProperty = $reflectionClass->getProperty(name: 'dbConfig');
    $dbConfigProperty->setAccessible(accessible: true);

    $dbConfig = $dbConfigProperty->getValue(object: new BackupService);
    $config   = config(key: 'database.connections.mysql');

    expect(value: $dbConfig)
        ->toBeArray()
        ->toHaveKey(key: 'host')
        ->toHaveKey(key: 'port')
        ->toHaveKey(key: 'database')
        ->toHaveKey(key: 'username')
        ->toHaveKey(key: 'password')
        ->and(value: $dbConfig['host'])
        ->toBe(expected: $config['host'])
        ->and(value: $dbConfig['port'])
        ->toBe(expected: $config['port'])
        ->and(value: $dbConfig['database'])
        ->toBe(expected: $config['database'])
        ->and(value: $dbConfig['username'])
        ->toBe(expected: $config['username'])
        ->and(value: $dbConfig['password'])
        ->toBe(expected: $dbConfig['password']);
});

test(description: 'setFileName sets fileName and filePath properties when given non-empty string', closure: function (): void
{
    $service      = app(abstract: BackupServiceInterface::class);
    $disk         = Storage::disk(name: 'local');
    $reflection   = new ReflectionClass(objectOrClass: get_class(object: $service));
    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $disk);

    $result = $service->setFileName(fileName: 'test-backup.zip');

    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    $filePathProperty = $reflection->getProperty(name: 'filePath');
    $filePathProperty->setAccessible(accessible: true);

    expect(value: $result)->toBe(expected: $service)
        ->and(value: $fileNameProperty->getValue(object: $service))
        ->toBe(expected: 'test-backup.zip')
        ->and(value: $filePathProperty->getValue(object: $service))
        ->toBe(expected: $disk->path(path: 'test-backup.zip'));
});

test(description: 'setSqlName sets sqlName and sqlPath properties when given non-empty string', closure: function (): void
{
    $service      = app(abstract: BackupServiceInterface::class);
    $disk         = Storage::disk(name: 'local');
    $reflection   = new ReflectionClass(objectOrClass: get_class(object: $service));
    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $disk);

    $result = $service->setSqlName(sqlName: 'test-database.sql');

    $sqlNameProperty = $reflection->getProperty(name: 'sqlName');
    $sqlNameProperty->setAccessible(accessible: true);

    $sqlPathProperty = $reflection->getProperty(name: 'sqlPath');
    $sqlPathProperty->setAccessible(accessible: true);

    expect(value: $result)
        ->toBe(expected: $service)
        ->and(value: $sqlNameProperty->getValue(object: $service))
        ->toBe(expected: 'test-database.sql')
        ->and(value: $sqlPathProperty->getValue(object: $service))
        ->toBe(expected: $disk->path(path: 'test-database.sql'));
});

test(description: '__invoke calls all required methods in sequence when error is not set', closure: function (): void
{
    $customImplementation = new TestBackupServiceImplementation();

    expect(value: $customImplementation->__invoke())
        ->toBeTrue()
        ->and(value: $customImplementation->methodsCalled)
        ->toBe(expected: [
            'createDatabaseDump',
            'createBackupFile',
            'storeBackup',
        ]);
});

test(description: 'setZipPath sets error when config is not an array', closure: function (): void
{
    $service = app(abstract: BackupServiceInterface::class);

    Config::set(key: 'backups');

    $reflection = new ReflectionClass(objectOrClass: get_class(object: $service));
    $method     = $reflection->getMethod(name: 'setZipPath');
    $method->setAccessible(accessible: true);

    $result = $method->invoke(object: $service);

    expect(value: $service->error)
        ->not->toBeNull()
        ->and(value: $result)
        ->toBe(expected: $service);
});

test(description: 'setZipPath uses custom date format and zip file name from config', closure: function (): void
{
    $service      = app(abstract: BackupServiceInterface::class);
    $disk         = Storage::disk(name: 'local');
    $reflection   = new ReflectionClass(objectOrClass: get_class(object: $service));
    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $disk);

    Config::set(key: 'backups', value: [
        'date_format' => 'Y_m_d',
        'zip_file'    => 'custom-backup-:date.zip',
    ]);

    $method = $reflection->getMethod(name: 'setZipPath');
    $method->setAccessible(accessible: true);
    $result = $method->invoke(object: $service);

    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    $filePathProperty = $reflection->getProperty(name: 'filePath');
    $filePathProperty->setAccessible(accessible: true);

    $expectedDate     = now()->format(format: 'Y_m_d');
    $expectedFileName = "custom-backup-{$expectedDate}.zip";

    expect(value: $result)
        ->toBe(expected: $service)
        ->and(value: $fileNameProperty->getValue(object: $service))
        ->toBe(expected: $expectedFileName)
        ->and(value: $filePathProperty->getValue(object: $service))
        ->toBe(expected: $disk->path(path: $expectedFileName));
});

test(description: 'setFileName does not change properties when given an empty string', closure: function (): void
{
    $service = app(abstract: BackupServiceInterface::class);
    $disk    = Storage::disk(name: 'local');

    $reflection = new ReflectionClass(objectOrClass: get_class(object: $service));

    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $disk);

    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);
    $fileNameProperty->setValue(objectOrValue: $service, value: 'initial-backup.zip');

    $filePathProperty = $reflection->getProperty(name: 'filePath');
    $filePathProperty->setAccessible(accessible: true);
    $filePathProperty->setValue(objectOrValue: $service, value: $disk->path(path: 'initial-backup.zip'));

    $initialFileName = $fileNameProperty->getValue(object: $service);
    $initialFilePath = $filePathProperty->getValue(object: $service);

    $result = $service->setFileName(fileName: '');

    expect(value: $result)->toBe(expected: $service)
        ->and(value: $fileNameProperty->getValue(object: $service))
        ->toBe(expected: $initialFileName)
        ->and(value: $filePathProperty->getValue(object: $service))
        ->toBe(expected: $initialFilePath);
});

test(description: 'setSqlName does not change properties when given an empty string', closure: function (): void
{
    $service = app(abstract: BackupServiceInterface::class);
    $disk    = Storage::disk(name: 'local');

    $reflection = new ReflectionClass(objectOrClass: get_class(object: $service));

    $diskProperty = $reflection->getProperty(name: 'disk');
    $diskProperty->setAccessible(accessible: true);
    $diskProperty->setValue(objectOrValue: $service, value: $disk);

    $sqlNameProperty = $reflection->getProperty(name: 'sqlName');
    $sqlNameProperty->setAccessible(accessible: true);
    $sqlNameProperty->setValue(objectOrValue: $service, value: 'initial-database.sql');

    $sqlPathProperty = $reflection->getProperty(name: 'sqlPath');
    $sqlPathProperty->setAccessible(accessible: true);
    $sqlPathProperty->setValue(objectOrValue: $service, value: $disk->path(path: 'initial-database.sql'));

    $initialSqlName = $sqlNameProperty->getValue(object: $service);
    $initialSqlPath = $sqlPathProperty->getValue(object: $service);

    $result = $service->setSqlName(sqlName: '');

    expect(value: $result)->toBe(expected: $service)
        ->and(value: $sqlNameProperty->getValue(object: $service))
        ->toBe(expected: $initialSqlName)
        ->and(value: $sqlPathProperty->getValue(object: $service))
        ->toBe(expected: $initialSqlPath);
});

it(description: 'it returns false on invoke', closure: function (): void
{
    $service        = app(abstract: BackupServiceInterface::class);
    $service->error = 'Something went wrong';

    expect(value: $service())
        ->toBeFalse();
});

it(description: 'returns false when database dump fails', closure: function (): void
{
    config(key: [
        'database.connections.mysql' => [
            'host'     => 'non-existent-host',
            'port'     => 99999,
            'database' => 'non_existent_db',
            'username' => 'invalid_user',
            'password' => 'invalid_pass',
        ],
    ]);

    Artisan::call(command: 'config:clear');

    $service        = app(abstract: BackupServiceInterface::class);
    $service->error = null;

    expect(value: $service())
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

it(description: 'returns false when createBackupFile fails', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    MySqlDumper::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'setHost')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPort')
        ->andReturn(Mockery::self())
        ->shouldReceive('setDbName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setUserName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPassword')
        ->andReturn(Mockery::self())
        ->shouldReceive('dumpToFile')
        ->andReturn(true);

    $service       = app(abstract: BackupServiceInterface::class);
    $reflection    = new ReflectionClass(objectOrClass: $service);
    $errorProperty = $reflection->getProperty(name: 'error');
    $errorProperty->setAccessible(accessible: true);
    $errorProperty->setValue(objectOrValue: $service, value: null);

    Zip::shouldReceive('create')
        ->andThrow(exception: new Exception(message: 'Failed to create zip file'));

    expect(value: $service())
        ->toBeFalse()
        ->and(value: $service->error)
        ->not->toBeNull();
});

it(description: 'returns true when entire backup process succeeds', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    MySqlDumper::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'setHost')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPort')
        ->andReturn(Mockery::self())
        ->shouldReceive('setDbName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setUserName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPassword')
        ->andReturn(Mockery::self())
        ->shouldReceive('dumpToFile')
        ->andReturn(true);

    Zip::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'add')
        ->times(2)
        ->andReturn(Mockery::self())
        ->shouldReceive('close')
        ->once()
        ->andReturn(true);

    Zip::shouldReceive('check')
        ->andReturn(true);

    Gdrive::shouldReceive('put')
        ->withAnyArgs()
        ->once()
        ->andReturnNull();

    $service = app(abstract: BackupServiceInterface::class);

    expect(value: $service())
        ->toBeTrue()
        ->and(value: $service->error)
        ->toBeNull();
});

it(description: 'uses fallback path when backups.upload_to config is not a string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    config(key: ['backups.upload_to' => null]);

    MySqlDumper::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'setHost')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPort')
        ->andReturn(Mockery::self())
        ->shouldReceive('setDbName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setUserName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPassword')
        ->andReturn(Mockery::self())
        ->shouldReceive('dumpToFile')
        ->andReturn(true);

    Zip::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'add')
        ->times(2)
        ->andReturn(Mockery::self())
        ->shouldReceive('close')
        ->once()
        ->andReturn(true);

    Zip::shouldReceive('check')
        ->andReturn(true);

    $expectedFileName = 'backup-' . now()->format(format: 'Y-m-d-H-i-s') . '.zip';
    $expectedPath     = 'backups/' . $expectedFileName;

    Gdrive::shouldReceive('put')
        ->withArgs(argsOrClosure: fn (string $path) => $path === $expectedPath)
        ->once()
        ->andReturnNull();

    $service          = app(abstract: BackupServiceInterface::class);
    $service->error   = null;
    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);
    $fileNameProperty->setValue(objectOrValue: $service, value: $expectedFileName);

    expect(value: $service())
        ->toBeTrue();
});

it(description: 'uses configured path when backups.upload_to config is a string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    $configPath = 'custom/path/:file';
    config(key: ['backups.upload_to' => $configPath]);

    MySqlDumper::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'setHost')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPort')
        ->andReturn(Mockery::self())
        ->shouldReceive('setDbName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setUserName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPassword')
        ->andReturn(Mockery::self())
        ->shouldReceive('dumpToFile')
        ->andReturn(true);

    Zip::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'add')
        ->times(2)
        ->andReturn(Mockery::self())
        ->shouldReceive('close')
        ->once()
        ->andReturn(true);

    Zip::shouldReceive('check')
        ->andReturn(true);

    $expectedFileName = 'backup-' . now()->format(format: 'Y-m-d-H-i-s') . '.zip';
    $expectedPath     = 'custom/path/' . $expectedFileName;

    Gdrive::shouldReceive('put')
        ->withArgs(argsOrClosure: fn (string $path) => $path === $expectedPath)
        ->once()
        ->andReturnNull();

    $service = app(abstract: BackupServiceInterface::class);

    $service->error = null;

    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);
    $fileNameProperty->setValue(objectOrValue: $service, value: $expectedFileName);

    expect(value: $service())
        ->toBeTrue();
});

it(description: 'returns false and sets error when storeBackup throws an exception', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    config(key: ['backups.upload_to' => 'backups/:file']);

    MySqlDumper::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'setHost')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPort')
        ->andReturn(Mockery::self())
        ->shouldReceive('setDbName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setUserName')
        ->andReturn(Mockery::self())
        ->shouldReceive('setPassword')
        ->andReturn(Mockery::self())
        ->shouldReceive('dumpToFile')
        ->andReturn(true);

    Zip::shouldReceive('create')
        ->andReturn(Mockery::self())
        ->getMock()
        ->shouldReceive(methodNames: 'add')
        ->times(2)
        ->andReturn(Mockery::self())
        ->shouldReceive('close')
        ->once()
        ->andReturn(true);

    Zip::shouldReceive('check')
        ->andReturn(true);

    $expectedErrorMessage = 'Failed to upload backup to Google Drive';

    Gdrive::shouldReceive('put')
        ->andThrow(exception: new Exception(message: $expectedErrorMessage));

    $service        = app(abstract: BackupServiceInterface::class);
    $service->error = null;

    expect(value: $service())
        ->toBeFalse()
        ->and(value: $service->error)
        ->toBe(expected: $expectedErrorMessage);
});

it(description: 'uses default date format when date_format config is not set or not a string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    config(key: ['backups' => ['other_setting' => 'value']]);

    $service          = app(abstract: BackupServiceInterface::class);
    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    $fileName = $fileNameProperty->getValue(object: $service);

    expect(value: str_replace(search: ['.zip', 'backup-'], replace: '', subject: $fileName))
        ->toMatch(expression: '/^\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}$/');
});

it(description: 'uses custom date format when date_format config is set as string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    $customFormat = 'YmdHis';
    config(key: ['backups' => ['date_format' => $customFormat]]);

    $service          = app(abstract: BackupServiceInterface::class);
    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    $fileName = $fileNameProperty->getValue(object: $service);

    expect(value: str_replace(search: ['.zip', 'backup-'], replace: '', subject: $fileName))
        ->toMatch(expression: '/^\d{14}$/');
});

it(description: 'uses default zip file key when zip_file config is not set or not a string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    config(key: ['backups' => ['other_setting' => 'value']]);

    $service          = app(abstract: BackupServiceInterface::class);
    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    expect(value: $fileNameProperty->getValue(object: $service))
        ->toStartWith(expected: 'backup-')
        ->toEndWith(expected: '.zip');
});

it(description: 'uses custom zip file key when zip_file config is set as string', closure: function (): void
{
    Artisan::call(command: 'config:clear');

    $customZipFile = 'custom-backup-:date.zip';
    config(key: ['backups' => ['zip_file' => $customZipFile]]);

    $service          = app(abstract: BackupServiceInterface::class);
    $reflection       = new ReflectionClass(objectOrClass: $service);
    $fileNameProperty = $reflection->getProperty(name: 'fileName');
    $fileNameProperty->setAccessible(accessible: true);

    expect(value: $fileNameProperty->getValue(object: $service))
        ->toStartWith(expected: 'custom-backup-')
        ->toEndWith(expected: '.zip');
});
