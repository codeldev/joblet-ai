<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Blog\PostGenerationServiceInterface;
use App\Exceptions\Blog\BlogIdeaNotFoundDuringQueuedJobException;
use App\Jobs\ProcessBlogIdeaJob;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;

beforeEach(closure: function (): void
{
    $this->testIdeaId = Str::uuid()->toString();
});

describe(description: 'ProcessBlogIdeaJob', tests: function (): void
{
    afterEach(closure: function (): void
    {
        Mockery::close();
    });

    it('implements ShouldQueue interface', function (): void
    {
        expect(value: new ProcessBlogIdeaJob(ideaId: $this->testIdeaId))
            ->toBeInstanceOf(class: ShouldQueue::class);
    });

    it('has correct properties', function (): void
    {
        $job = new ProcessBlogIdeaJob(ideaId: $this->testIdeaId);

        expect(value: $job->ideaId)
            ->toBe(expected: $this->testIdeaId)
            ->and(value: $job->tries)
            ->toBe(expected: 3)
            ->and(value: $job->timeout)
            ->toBe(expected: 600);
    });

    it('returns correct backoff times', function (): void
    {
        expect(value: new ProcessBlogIdeaJob(ideaId: $this->testIdeaId)->backoff())
            ->toBeArray()
            ->toContain(needles: 60)
            ->toContain(needles: 300)
            ->toContain(needles: 600);
    });

    it('handles post generation successfully', function (): void
    {
        $mockService = Mockery::mock(args: PostGenerationServiceInterface::class);
        $mockService->shouldReceive(methodNames: 'handle')
            ->once()
            ->withNoArgs();

        $this->app->bind(
            abstract: PostGenerationServiceInterface::class,
            concrete: function ($app, array $parameters) use ($mockService)
            {
                if (($parameters['ideaId'] ?? null) === $this->testIdeaId)
                {
                    return $mockService;
                }

                throw new BindingResolutionException(message: 'Invalid parameters');
            }
        );

        $job = new ProcessBlogIdeaJob(ideaId: $this->testIdeaId)
            ->withFakeQueueInteractions();

        $job->handle();
        $job->assertNotFailed();
    });

    it('fails gracefully when service throws an exception', function (): void
    {
        $job = new ProcessBlogIdeaJob(ideaId: $this->testIdeaId)
            ->withFakeQueueInteractions();

        $job->handle();
        $job->assertFailed()->assertFailedWith(
            exception: BlogIdeaNotFoundDuringQueuedJobException::class
        );
    });
});
