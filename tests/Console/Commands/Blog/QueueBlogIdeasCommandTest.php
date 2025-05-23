<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Console\Commands\Blog\QueueBlogIdeasCommand;
use App\Jobs\ProcessBlogIdeaJob;
use App\Models\BlogIdea;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(closure: function (): void
{
    $this->mockDbTransactionSuccess = function (): void
    {
        DB::shouldReceive('transaction')
            ->andReturnUsing(args: fn ($callback) => $callback());
    };

    $this->mockDbTransactionFailure = function (string $errorMessage = 'Database error'): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new RuntimeException(message: $errorMessage));
    };

    Config::set(key: 'blog.ideas.delay', value: 30);

    Queue::fake();
});

describe(description: 'QueueBlogIdeasCommand', tests: function (): void
{
    it('handles no unprocessed ideas', function (): void
    {
        $this->artisan(
            command   : 'blog:ideas:queue',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        Queue::assertNothingPushed();
    });

    it('queues unprocessed ideas with correct delays', function (): void
    {
        $idea1 = BlogIdea::factory()->create(attributes: [
            'schedule_date' => now()->subDay(),
            'queued_at'     => null,
        ]);

        $idea2 = BlogIdea::factory()->create(attributes: [
            'schedule_date' => now(),
            'queued_at'     => null,
        ]);

        $idea3 = BlogIdea::factory()->create(attributes: [
            'schedule_date' => now()->addDay(),
            'queued_at'     => null,
        ]);

        DB::shouldReceive('transaction')
            ->times(limit: 3)
            ->andReturnUsing(args: fn ($callback) => $callback());

        $this->artisan(command: 'blog:ideas:queue', parameters: ['--debug' => true])
            ->assertExitCode(exitCode: 0);

        Queue::assertPushed(
            job     : ProcessBlogIdeaJob::class,
            callback: 3
        );

        Queue::assertPushed(
            job     : ProcessBlogIdeaJob::class,
            callback: fn (ProcessBlogIdeaJob $job) => $job->ideaId === $idea1->id
        );

        Queue::assertPushed(
            job     : ProcessBlogIdeaJob::class,
            callback: fn (ProcessBlogIdeaJob $job) => $job->ideaId === $idea2->id
        );

        Queue::assertPushed(
            job     : ProcessBlogIdeaJob::class,
            callback: fn (ProcessBlogIdeaJob $job) => $job->ideaId === $idea3->id
        );

        expect(value: $idea1->fresh()->queued_at)
            ->not->toBeNull()
            ->and(value: $idea2->fresh()->queued_at)
            ->not->toBeNull()
            ->and(value: $idea3->fresh()->queued_at)
            ->not->toBeNull();
    });

    it('uses default delay minutes when config is invalid', function (): void
    {
        Config::set(key: 'blog.ideas.delay', value: 'invalid');

        $idea = BlogIdea::factory()->create(attributes: [
            'schedule_date' => now(),
            'queued_at'     => null,
        ]);

        ($this->mockDbTransactionSuccess)();

        $this->artisan(
            command   : 'blog:ideas:queue',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        Queue::assertPushed(
            job     : ProcessBlogIdeaJob::class,
            callback: 1
        );

        expect(value: $idea->fresh()->queued_at)
            ->not->toBeNull();
    });

    it('handles database errors during idea update', function (): void
    {
        BlogIdea::factory()->create(attributes: [
            'schedule_date' => now(),
            'queued_at'     => null,
        ]);

        ($this->mockDbTransactionFailure)('Database error during update');

        $this->artisan(
            command   : 'blog:ideas:queue',
            parameters: ['--debug' => true]
        )
            ->expectsOutput(output: 'Database error during update')
            ->assertExitCode(exitCode: 1);
    });

    it('displays messages correctly when debug is true', function (): void
    {
        $command = new QueueBlogIdeasCommand();
        $output  = new BufferedOutput();

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
        $command = new QueueBlogIdeasCommand();
        $output  = new BufferedOutput();

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
