<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Blog\ContentImagesGenerationServiceInterface;
use App\Exceptions\Blog\BlogPostNotFoundDuringImageGenerationException;
use App\Jobs\ProcessBlogContentImagesJob;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;

beforeEach(closure: function (): void
{
    $this->testPostId = Str::uuid()->toString();
});

describe(description: 'ProcessBlogContentImagesJob', tests: function (): void
{
    it('implements ShouldQueue interface', function (): void
    {
        expect(value: new ProcessBlogContentImagesJob(postId: $this->testPostId))
            ->toBeInstanceOf(class: ShouldQueue::class);
    });

    it('has correct properties', function (): void
    {
        expect(new ProcessBlogContentImagesJob(postId: $this->testPostId))
            ->postId->toBe(expected: $this->testPostId)
            ->tries->toBe(expected: 3)
            ->timeout->toBe(expected: 240);
    });

    it('returns correct backoff times', function (): void
    {
        expect(value: new ProcessBlogContentImagesJob(postId: $this->testPostId)->backoff())
            ->toBeArray()
            ->toContain(needles: 60)
            ->toContain(needles: 300)
            ->toContain(needles: 600);
    });

    it('handles content images generation successfully', function (): void
    {
        $mockService = Mockery::mock(args: ContentImagesGenerationServiceInterface::class);
        $mockService->shouldReceive(methodNames: 'handle')
            ->once()
            ->withNoArgs();

        $this->app->bind(
            abstract: ContentImagesGenerationServiceInterface::class,
            concrete: function ($app, array $parameters) use ($mockService)
            {
                if (($parameters['postId'] ?? null) === $this->testPostId)
                {
                    return $mockService;
                }

                throw new BindingResolutionException(message: 'Invalid parameters');
            }
        );

        $job = new ProcessBlogContentImagesJob(postId: $this->testPostId)
            ->withFakeQueueInteractions();

        $job->handle();
        $job->assertNotFailed();
    });

    it('fails gracefully when service throws an exception', function (): void
    {
        $job = new ProcessBlogContentImagesJob(postId: $this->testPostId)
            ->withFakeQueueInteractions();

        $job->handle();
        $job->assertFailed()->assertFailedWith(
            exception: BlogPostNotFoundDuringImageGenerationException::class
        );
    });
});
