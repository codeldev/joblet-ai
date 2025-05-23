<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Console\Commands\Blog\ProcessBlogIdeasCommand;
use App\Enums\StorageDiskEnum;
use App\Models\BlogIdea;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(closure: function (): void
{
    Storage::fake(disk: StorageDiskEnum::BLOG_IDEAS->value);
    Storage::fake(disk: StorageDiskEnum::BLOG_UNPROCESSABLE->value);

    $this->ideasDisk  = Storage::disk(name: StorageDiskEnum::BLOG_IDEAS->value);
    $this->failedDisk = Storage::disk(name: StorageDiskEnum::BLOG_UNPROCESSABLE->value);

    Config::set(key: 'blog.post.schedule', value: 2);

    $this->mockDbTransactionSuccess = static function (): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(args: fn ($callback) => $callback());
    };

    $this->mockDbTransactionFailure = static function (string $errorMessage = 'Database error'): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new RuntimeException(message: $errorMessage));
    };
});

afterEach(closure: function (): void
{
    Mockery::close();
});

describe(description: 'ProcessBlogIdeasCommand', tests: function (): void
{
    it('processes valid JSON files and stores them in the database', function (): void
    {
        $validData = [
            'topic'        => 'Test Topic',
            'keywords'     => 'test, keywords',
            'focus'        => 'Test focus',
            'requirements' => 'Test requirements',
            'additional'   => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'valid-idea.json',
            contents: json_encode(value: $validData, flags: JSON_THROW_ON_ERROR)
        );

        ($this->mockDbTransactionSuccess)();

        $this->artisan(command: 'blog:ideas:process', parameters: ['--debug' => true])
            ->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'valid-idea.json');
    });

    it('handles missing required keys in JSON files', function (): void
    {
        $invalidData = [
            'topic'      => 'Test Topic',
            'focus'      => 'Test focus',
            'additional' => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'invalid-idea.json',
            contents: json_encode(value: $invalidData, flags: JSON_THROW_ON_ERROR)
        );

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'invalid-idea.json');
        $this->failedDisk->assertExists(path: 'invalid-idea.json');
    });

    it('handles invalid JSON files', function (): void
    {
        $this->ideasDisk->put(
            path    : 'invalid-json.json',
            contents: '{invalid json'
        );

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'invalid-json.json');
        $this->failedDisk->assertExists(path: 'invalid-json.json');
    });

    it('handles empty values for required keys', function (): void
    {
        $emptyValueData = [
            'topic'        => 'Test Topic',
            'keywords'     => '',
            'focus'        => 'Test focus',
            'requirements' => 'Test requirements',
            'additional'   => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'empty-value.json',
            contents: json_encode(value: $emptyValueData, flags: JSON_THROW_ON_ERROR)
        );

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'empty-value.json');
        $this->failedDisk->assertExists(path: 'empty-value.json');
    });

    it('handles exceptions during database operations', function (): void
    {
        $validData = [
            'topic'        => 'Test Topic',
            'keywords'     => 'test, keywords',
            'focus'        => 'Test focus',
            'requirements' => 'Test requirements',
            'additional'   => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'db-error.json',
            contents: json_encode(value: $validData, flags: JSON_THROW_ON_ERROR)
        );

        ($this->mockDbTransactionFailure)('Database error');

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'db-error.json');
        $this->failedDisk->assertExists(path: 'db-error.json');
    });

    it('handles no JSON files to process', function (): void
    {
        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);
    });

    it('calculates the next schedule date correctly', function (): void
    {
        $validData = [
            'topic'        => 'Test Topic',
            'keywords'     => 'test, keywords',
            'focus'        => 'Test focus',
            'requirements' => 'Test requirements',
            'additional'   => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'schedule-test.json',
            contents: json_encode(value: $validData, flags: JSON_THROW_ON_ERROR)
        );

        BlogIdea::factory()->create(attributes: [
            'schedule_date' => now()->subDays(value: 3),
        ]);

        ($this->mockDbTransactionSuccess)();

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'schedule-test.json');
    });

    it('uses default schedule days when config is invalid', function (): void
    {
        Config::set('blog.post.schedule', 'invalid');

        $validData = [
            'topic'        => 'Test Topic',
            'keywords'     => 'test, keywords',
            'focus'        => 'Test focus',
            'requirements' => 'Test requirements',
            'additional'   => 'Test additional info',
        ];

        $this->ideasDisk->put(
            path    : 'default-schedule.json',
            contents: json_encode(value: $validData, flags: JSON_THROW_ON_ERROR)
        );

        BlogIdea::factory()->create(attributes: [
            'schedule_date' => now()->subDays(value: 3),
        ]);

        ($this->mockDbTransactionSuccess)();

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'default-schedule.json');
    });

    it('uses tomorrow as schedule date when no previous ideas exist', function (): void
    {
        $validData = [
            'topic'        => 'First Topic',
            'keywords'     => 'first, keywords',
            'focus'        => 'First focus',
            'requirements' => 'First requirements',
            'additional'   => 'First additional info',
        ];

        $this->ideasDisk->put(
            path    : 'first-idea.json',
            contents: json_encode(value: $validData, flags: JSON_THROW_ON_ERROR)
        );

        ($this->mockDbTransactionSuccess)();

        $this->artisan(
            command   : 'blog:ideas:process',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        $this->ideasDisk->assertMissing(path: 'first-idea.json');
    });

    it('displays messages correctly when debug is true', function (): void
    {
        $output  = new BufferedOutput();
        $command = new ProcessBlogIdeasCommand();

        $reflectionClass  = new ReflectionClass(objectOrClass: $command);
        $reflectionMethod = $reflectionClass->getMethod(name: 'displayMessage');
        $reflectionMethod->setAccessible(accessible: true);

        $reflectionProperty = $reflectionClass->getProperty(name: 'showOutput');
        $reflectionProperty->setAccessible(accessible: true);
        $reflectionProperty->setValue(objectOrValue: $command, value: true);

        $command->setOutput(
            output: new OutputStyle(input: new ArrayInput(parameters: []), output: $output)
        );

        $reflectionMethod->invoke($command, 'Test info message', 'info');
        $reflectionMethod->invoke($command, 'Test error message', 'error');
        $reflectionMethod->invoke($command, 'Test default message', 'default');

        expect(value: $output->fetch())
            ->toContain(needles: 'Test info message')
            ->toContain(needles: 'Test error message')
            ->toContain(needles: 'Test default message');
    });

    it('does not display messages when debug is false', function (): void
    {
        $output  = new BufferedOutput();
        $command = new ProcessBlogIdeasCommand();

        $reflectionClass  = new ReflectionClass(objectOrClass: $command);
        $reflectionMethod = $reflectionClass->getMethod(name: 'displayMessage');
        $reflectionMethod->setAccessible(accessible: true);

        $reflectionProperty = $reflectionClass->getProperty(name: 'showOutput');
        $reflectionProperty->setAccessible(accessible: true);
        $reflectionProperty->setValue(objectOrValue: $command, value: false);

        $command->setOutput(
            output: new OutputStyle(input: new ArrayInput(parameters: []), output: $output)
        );

        $reflectionMethod->invoke($command, 'This should not be displayed', 'info');

        expect(value: $output->fetch())
            ->toBe(expected: '');
    });
});
