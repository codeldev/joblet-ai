<?php

declare(strict_types=1);

namespace App\Console\Commands\Blog;

use App\Contracts\Console\Commands\Blog\QueueBlogIdeasCommandInterface;
use App\Jobs\ProcessBlogIdeaJob;
use App\Models\BlogIdea;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    name        : 'blog:ideas:queue',
    description : 'Queue blog ideas for processing with 30-minute intervals'
)]
final class QueueBlogIdeasCommand extends Command implements QueueBlogIdeasCommandInterface
{
    private bool $showOutput = false;

    private readonly int $delayMins;

    private int $delay;

    /** @var array<string, string> */
    private array $messages = [];

    public function __construct()
    {
        $this->setMessages();

        $this->delayMins = $this->getDelayMinutes();
        $this->delay     = $this->delayMins;

        parent::__construct();
    }

    public function handle(): int
    {
        $this->showOutput = (bool) $this->option(key: 'debug');

        $this->displayMessage(message: $this->messages['checking']);

        $ideas = $this->getUnprocessedIdeas();

        return $ideas->isEmpty()
            ? $this->nothingToProcess()
            : $this->processIdeas(ideas: $ideas);
    }

    protected function configure(): void
    {
        $this->addOption(
            name       : 'debug',
            description: 'Show debug output'
        );
    }

    private function getDelayMinutes(): int
    {
        $defaultMinutes = 10;

        /** @var mixed $delayConfig */
        $delayConfig = config(
            key    : 'blog.ideas.delay',
            default: $defaultMinutes
        );

        return is_numeric(value: $delayConfig)
            ? (int) $delayConfig
            : $defaultMinutes;
    }

    private function nothingToProcess(): int
    {
        $this->displayMessage(message: $this->messages['noneFound']);

        return self::SUCCESS;
    }

    /** @param Collection<int, BlogIdea> $ideas */
    private function processIdeas(Collection $ideas): int
    {
        try
        {
            $this->displayMessage(message: trans(
                key    : $this->messages['countFound'],
                replace: ['count' => $ideas->count()]
            ));

            $ideas->each(callback: fn (BlogIdea $idea) => $this->processIdea(idea: $idea));

            $this->displayMessage(message: $this->messages['finished']);

            return self::SUCCESS;
        }
        catch (Throwable $exception)
        {
            $this->displayMessage(
                message: $exception->getMessage(),
                type   : 'error'
            );

            return self::FAILURE;
        }
    }

    /** @return Collection<int, BlogIdea> */
    private function getUnprocessedIdeas(): Collection
    {
        return BlogIdea::query()
            ->whereNull(columns: 'queued_at')
            ->orderBy(column: 'schedule_date')
            ->get();
    }

    /** @throws Throwable */
    private function processIdea(BlogIdea $idea): void
    {
        $this->displayMessage(message: trans(
            key    : $this->messages['queued'],
            replace: [
                'id'    => $idea->id,
                'delay' => $this->delay,
            ]
        ));

        ProcessBlogIdeaJob::dispatch(ideaId: $idea->id)
            ->delay(delay: Carbon::now()->addMinutes(value: $this->delay));

        $this->updateIdea(idea: $idea);

        $this->delay += $this->delayMins;
    }

    /** @throws Throwable */
    private function updateIdea(BlogIdea $idea): void
    {
        try
        {
            DB::transaction(callback: static function () use ($idea): void
            {
                $idea->updateQuietly(attributes: [
                    'queued_at' => now(),
                ]);
            });
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    private function displayMessage(string $message, string $type = 'info'): void
    {
        if ($this->showOutput)
        {
            match ($type)
            {
                'error' => $this->error(string: $message),
                default => $this->info(string: $message),
            };
        }
    }

    private function setMessages(): void
    {
        $this->messages = [
            'checking'   => trans(key: 'blog.command.idea.queue.checking'),
            'noneFound'  => trans(key: 'blog.command.idea.queue.none.found'),
            'countFound' => trans(key: 'blog.command.idea.queue.count.found'),
            'finished'   => trans(key: 'blog.command.idea.queue.finished'),
            'queued'     => trans(key: 'blog.command.idea.queue.queued'),
        ];
    }
}
