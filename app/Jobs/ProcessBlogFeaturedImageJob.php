<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\Blog\FeaturedImageGenerationServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

final class ProcessBlogFeaturedImageJob implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    public int $tries = 3;

    public int $timeout = 240;

    public function __construct(public readonly string $postId) {}

    /** @return array<int, int> */
    public function backoff(): array
    {
        /** @var string $timers */
        $timers = config(
            key    : 'blog.job.attempts',
            default: '60|300|600'
        );

        /** @var array<int, string> $explodedValues */
        $explodedValues = str(string: $timers)
            ->explode(delimiter: '|')
            ->values()
            ->toArray();

        /** @var array<int, int> $result */
        $result = array_map(callback: 'intval', array: $explodedValues);

        return $result;
    }

    public function handle(): void
    {
        try
        {
            /** @var FeaturedImageGenerationServiceInterface $service */
            $service = app()->make(
                abstract  : FeaturedImageGenerationServiceInterface::class,
                parameters: ['postId' => $this->postId]
            );

            $service->handle();
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            $this->fail(exception: $exception);
        }
    }
}
