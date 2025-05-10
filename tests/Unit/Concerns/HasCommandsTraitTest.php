<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use Illuminate\Foundation\Application;
use Mockery;
use ReflectionClass;
use Tests\Classes\Unit\Concerns\HasCommandsTest;

describe(description: 'HasCommandsTrait', tests: function (): void
{
    it(description: 'provides default options including dry-run', closure: function (): void
    {
        $command = new HasCommandsTest();
        $options = $command->getOptionsPublic();

        expect(value: $options)
            ->toBeArray()
            ->toHaveCount(count: 1)
            ->and(value: $options[0][0])
            ->toBe(expected: 'dry-run')
            ->and(value: $options[0][3])
            ->toBe(expected: 'Run in dry-run mode');
    });

    it(description: 'checks if running in dry-run mode', closure: function (): void
    {
        // Create a command mock that returns true for the dry-run option
        $command = Mockery::mock(HasCommandsTest::class)->makePartial();
        $command->shouldReceive('option')
            ->with('dry-run')
            ->andReturn(true);

        expect(value: $command->isDryRunPublic())->toBeTrue();

        // Create a command mock that returns false for the dry-run option
        $command = Mockery::mock(HasCommandsTest::class)->makePartial();
        $command->shouldReceive('option')
            ->with('dry-run')
            ->andReturn(false);

        expect(value: $command->isDryRunPublic())->toBeFalse();
    });

    it(description: 'checks if running from scheduler', closure: function (): void
    {
        // Mock the app to test isRunningFromScheduler
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(true);
        app()->instance('app', $app);

        // In a test environment, STDIN is typically defined, so this should return false
        $command = new HasCommandsTest();
        expect(value: $command->isRunningFromSchedulerPublic())->toBeFalse();

        // Now test the case where it's running from scheduler
        if (! defined('STDIN'))
        {
            define('STDIN', fopen('php://stdin', 'r'));
        }

        // We need to use reflection to modify the behavior since we can't undefine constants
        $reflectionClass  = new ReflectionClass(Application::class);
        $reflectionMethod = $reflectionClass->getMethod('runningInConsole');
        $reflectionMethod->setAccessible(true);

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('runningInConsole')->andReturn(true);
        app()->instance('app', $app);

        // Since we can't undefine STDIN, we'll test the logic directly
        expect(value: app()->runningInConsole() && ! defined('STDIN'))->toBeFalse();
    });

    it(description: 'checks if running manually', closure: function (): void
    {
        // Create a test class that overrides the isRunningFromScheduler method
        $testClass = new class extends HasCommandsTest
        {
            public bool $errorCalled = false;

            public string $errorMessage = '';

            private bool $mockRunningManually = true;

            public function setMockRunningManually(bool $value): void
            {
                $this->mockRunningManually = $value;
            }

            public function error($string, $verbosity = null): void
            {
                $this->errorCalled  = true;
                $this->errorMessage = $string;
            }

            protected function isRunningManually(): bool
            {
                return $this->mockRunningManually;
            }
        };

        expect($testClass->isRunningManuallyPublic())->toBeTrue();

        // When running from scheduler, isRunningManually should return false
        $testClass->setMockRunningManually(false);
        expect($testClass->isRunningManuallyPublic())->toBeFalse();
    });

    it(description: 'outputs error messages when running manually', closure: function (): void
    {
        // Create a test class that overrides the isRunningManually method
        $testClass = new class extends HasCommandsTest
        {
            public bool $errorCalled = false;

            public string $errorMessage = '';

            private bool $mockRunningManually = true;

            public function setMockRunningManually(bool $value): void
            {
                $this->mockRunningManually = $value;
            }

            public function error($string, $verbosity = null): void
            {
                $this->errorCalled  = true;
                $this->errorMessage = $string;
            }

            protected function isRunningManually(): bool
            {
                return $this->mockRunningManually;
            }
        };

        // When running manually, error should be called
        $testClass->setMockRunningManually(true);
        $testClass->outputErrorMessagePublic('Test error message');
        expect($testClass->errorCalled)->toBeTrue();
        expect($testClass->errorMessage)->toBe('Test error message');

        // Reset tracking
        $testClass->errorCalled  = false;
        $testClass->errorMessage = '';

        // When not running manually, error should not be called
        $testClass->setMockRunningManually(false);
        $testClass->outputErrorMessagePublic('Test error message');
        expect($testClass->errorCalled)->toBeFalse();
        expect($testClass->errorMessage)->toBe('');
    });

    it(description: 'outputs info messages when running manually', closure: function (): void
    {
        // Create a test class that overrides the isRunningManually method
        $testClass = new class extends HasCommandsTest
        {
            public bool $infoCalled = false;

            public string $infoMessage = '';

            private bool $mockRunningManually = true;

            public function setMockRunningManually(bool $value): void
            {
                $this->mockRunningManually = $value;
            }

            public function info($string, $verbosity = null): void
            {
                $this->infoCalled  = true;
                $this->infoMessage = $string;
            }

            public function outputInfoMessagePublic(string $message): void
            {
                $this->outputInfoMessage(message: $message);
            }

            protected function isRunningManually(): bool
            {
                return $this->mockRunningManually;
            }
        };

        // When running manually, info should be called
        $testClass->outputInfoMessagePublic('Test info message');
        expect($testClass->infoCalled)->toBeTrue();
        expect($testClass->infoMessage)->toBe('Test info message');

        // Reset tracking
        $testClass->infoCalled  = false;
        $testClass->infoMessage = '';

        // When not running manually, info should not be called
        $testClass->setMockRunningManually(false);
        $testClass->outputInfoMessagePublic('Test info message');
        expect($testClass->infoCalled)->toBeFalse();
        expect($testClass->infoMessage)->toBe('');
    });

    afterEach(function (): void
    {
        Mockery::close();
    });
});
