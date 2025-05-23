<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\Blog\PostGenerationServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

final class ProcessBlogIdeaJob implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(public readonly string $ideaId) {}

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
            /** @var PostGenerationServiceInterface $service */
            $service = app()->make(
                abstract  : PostGenerationServiceInterface::class,
                parameters: ['ideaId' => $this->ideaId]
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
