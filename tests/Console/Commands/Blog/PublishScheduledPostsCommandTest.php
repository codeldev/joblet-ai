<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Console\Commands\Blog\PublishScheduledPostsCommand;
use App\Enums\PostStatusEnum;
use App\Models\BlogPost;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(closure: function (): void
{
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

describe(description: 'PublishScheduledPostsCommand', tests: function (): void
{
    it('handles no scheduled posts for today', function (): void
    {
        $this->artisan(
            command   : 'blog:posts:publish',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);
    });

    it('publishes scheduled posts for today', function (): void
    {
        $post1 = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::SCHEDULED,
            'scheduled_at' => now()->today(),
            'published_at' => null,
        ]);

        $post2 = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::SCHEDULED,
            'scheduled_at' => now()->today(),
            'published_at' => null,
        ]);

        BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::SCHEDULED,
            'scheduled_at' => now()->addDay(),
            'published_at' => null,
        ]);

        BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::DRAFT,
            'scheduled_at' => now()->today(),
            'published_at' => null,
        ]);

        ($this->mockDbTransactionSuccess)();
        ($this->mockDbTransactionSuccess)();

        $this->artisan(
            command   : 'blog:posts:publish',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        expect(value: $post1->fresh()->status)
            ->toBe(expected: PostStatusEnum::PUBLISHED)
            ->and(value: $post1->fresh()->published_at)
            ->not->toBeNull()
            ->and(value: $post2->fresh()->status)
            ->toBe(expected: PostStatusEnum::PUBLISHED)
            ->and(value: $post2->fresh()->published_at)
            ->not->toBeNull();
    });

    it('handles database errors during post update', function (): void
    {
        $post = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::SCHEDULED,
            'scheduled_at' => now()->today(),
            'published_at' => null,
        ]);

        ($this->mockDbTransactionFailure)('Database error during update');

        $this->artisan(
            command   : 'blog:posts:publish',
            parameters: ['--debug' => true]
        )->assertExitCode(exitCode: 0);

        expect(value: $post->fresh()->status)
            ->toBe(expected: PostStatusEnum::SCHEDULED)
            ->and(value: $post->fresh()->published_at)
            ->toBeNull();
    });

    it('displays messages correctly when debug is true', function (): void
    {
        $output  = new BufferedOutput();
        $command = new PublishScheduledPostsCommand();

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
        $command = new PublishScheduledPostsCommand();

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
