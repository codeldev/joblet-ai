<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */

declare(strict_types=1);

use App\Services\Notifications\ExceptionNotificationService;

beforeEach(closure: function (): void
{
    $this->service             = new ExceptionNotificationService();
    $this->sampleExceptionData = [
        'url'     => 'https://example.com/error',
        'ip'      => '127.0.0.1',
        'user'    => 'Test User',
        'message' => 'Test error message',
        'file'    => '/path/to/file.php',
        'line'    => 42,
        'trace'   => [
            [
                'class'    => 'App\\TestClass',
                'function' => 'testMethod',
                'file'     => '/path/to/test.php',
                'line'     => 123,
            ],
            [
                'class'    => 'App\\AnotherClass',
                'function' => 'anotherMethod',
                'file'     => '/path/to/another.php',
                'line'     => 456,
            ],
        ],
    ];
});

test(description: 'buildReportData returns correctly formatted array', closure: function (): void
{
    expect(value: $result = $this->service->buildReportData(data: $this->sampleExceptionData))
        ->toBeArray()
        ->toHaveKeys(keys: ['url', 'ip', 'user', 'message', 'file', 'line'])
        ->and(value: $result['url'])
        ->toBe(expected: 'https://example.com/error')
        ->and(value: $result['ip'])
        ->toBe(expected: '127.0.0.1')
        ->and(value: $result['user'])
        ->toBe(expected: 'Test User')
        ->and(value: $result['message'])
        ->toBe(expected: 'Test error message')
        ->and(value: $result['file'])
        ->toBe(expected: '/path/to/file.php')
        ->and(value: $result['line'])
        ->toBe(expected: '42');
});

test(description: 'buildReportData handles missing values', closure: function (): void
{
    $incompleteData = [
        'message' => 'Only message is present',
    ];

    expect(value: $result = $this->service->buildReportData(data: $incompleteData))
        ->toBeArray()
        ->toHaveKeys(keys: ['url', 'ip', 'user', 'message', 'file', 'line'])
        ->and(value: $result['url'])
        ->toBe(expected: 'Unknown')
        ->and(value: $result['ip'])
        ->toBe(expected: 'Unknown')
        ->and(value: $result['user'])
        ->toBe(expected: 'Unknown')
        ->and(value: $result['message'])
        ->toBe(expected: 'Only message is present')
        ->and(value: $result['file'])
        ->toBe(expected: 'Unknown')
        ->and(value: $result['line'])
        ->toBe(expected: 'Unknown');
});

test(description: 'buildTraceData returns array of formatted trace lines', closure: function (): void
{
    expect(value: $result = $this->service->buildTraceData(traceData: $this->sampleExceptionData['trace']))
        ->toBeArray()
        ->toHaveCount(count: 2)
        ->and(value: $result[0])
        ->toBeString()
        ->not->toBeEmpty()
        ->and(value: $result[1])
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'buildTraceData handles non-array trace elements', closure: function (): void
{
    $invalidTraceData = [
        [
            'class'    => 'ValidClass',
            'function' => 'validMethod',
            'file'     => 'file.php',
            'line'     => 42,
        ],
        'not an array',
        null,
        123,
    ];

    expect(value: $result = $this->service->buildTraceData(traceData: $invalidTraceData))
        ->toBeArray()
        ->toHaveCount(count: 4)
        ->and(value: $result[0])
        ->toBeString()
        ->not->toBeEmpty()
        ->and(value: $result[1])
        ->toBe(expected: '')
        ->and(value: $result[2])
        ->toBe(expected: '')
        ->and(value: $result[3])
        ->toBe(expected: '');
});

test(description: 'getTraceLine returns empty string for non-array values', closure: function (): void
{
    $reflection = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method     = $reflection->getMethod(name: 'getTraceLine');
    $method->setAccessible(accessible: true);

    expect(value: $method->invokeArgs(object: $this->service, args: ['string value']))
        ->toBe(expected: '')
        ->and(value: $method->invokeArgs(object: $this->service, args: [null]))
        ->toBe(expected: '')
        ->and(value: $method->invokeArgs(object: $this->service, args: [123]))
        ->toBe(expected: '')
        ->and(value: $method->invokeArgs(object: $this->service, args: [true]))
        ->toBe(expected: '');
});

test(description: 'getTraceLine calls buildTraceLine for array values', closure: function (): void
{
    $reflection = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method     = $reflection->getMethod(name: 'getTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 'TestClass',
        'function' => 'testMethod',
        'file'     => 'test.php',
        'line'     => 42,
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'buildTraceLine formats trace line correctly', closure: function (): void
{
    $reflectionClass = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method          = $reflectionClass->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 'TestClass',
        'function' => 'testMethod',
        'file'     => 'test.php',
        'line'     => 42,
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty()
        ->toContain(needles: 'TestClass')
        ->toContain(needles: 'testMethod');
});

test(description: 'buildTraceLine handles missing trace elements', closure: function (): void
{
    $reflectionClass = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method          = $reflectionClass->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    expect(value: $method->invokeArgs(object: $this->service, args: [[]]))
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'toString converts string values correctly', closure: function (): void
{
    expect(value: $this->service->toString(value: 'Test string'))
        ->toBe(expected: 'Test string');
});

test(description: 'toString converts scalar values to strings', closure: function (): void
{
    expect(value: $this->service->toString(value: 123))
        ->toBe(expected: '123')
        ->and(value: $this->service->toString(value: 123.45))
        ->toBe(expected: '123.45')
        ->and(value: $this->service->toString(value: true))
        ->toBe(expected: '1')
        ->and(value: $this->service->toString(value: false))
        ->toBe(expected: '');
});

test(description: 'toString handles objects with __toString method', closure: function (): void
{
    $object = new class
    {
        public function __toString(): string
        {
            return 'Object string representation';
        }
    };

    expect(value: $this->service->toString(value: $object))
        ->toBe(expected: 'Object string representation');
});

test(description: 'toString returns Unknown for non-stringable values', closure: function (): void
{
    $resource = fopen(filename: 'php://memory', mode: 'rb');

    expect(value: $this->service->toString(value: new stdClass()))
        ->toBe(expected: 'Unknown')
        ->and(value: $this->service->toString(value: ['test' => 'value']))
        ->toBe(expected: 'Unknown')
        ->and(value: $this->service->toString(value: $resource))
        ->toBe(expected: 'Unknown');

    fclose(stream: $resource);
});

test(description: 'buildTraceLine handles non-string class values', closure: function (): void
{
    $reflection = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method     = $reflection->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 123,
        'function' => 'testMethod',
        'file'     => 'test.php',
        'line'     => 42,
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'buildTraceLine handles non-string function values', closure: function (): void
{
    $reflection = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method     = $reflection->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 'TestClass',
        'function' => 123,
        'file'     => 'test.php',
        'line'     => 42,
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'buildTraceLine handles non-string file values', closure: function (): void
{
    $reflection = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method     = $reflection->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 'TestClass',
        'function' => 'testMethod',
        'file'     => 123,
        'line'     => 42,
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'buildTraceLine handles non-numeric line values', closure: function (): void
{
    $reflectionClass = new ReflectionClass(objectOrClass: ExceptionNotificationService::class);
    $method          = $reflectionClass->getMethod(name: 'buildTraceLine');
    $method->setAccessible(accessible: true);

    $traceData = [
        'class'    => 'TestClass',
        'function' => 'testMethod',
        'file'     => 'test.php',
        'line'     => 'not a number',
    ];

    expect(value: $method->invokeArgs(object: $this->service, args: [$traceData]))
        ->toBeString()
        ->not->toBeEmpty();
});
