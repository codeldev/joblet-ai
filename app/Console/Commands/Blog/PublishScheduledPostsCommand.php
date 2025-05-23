<?php

declare(strict_types=1);

namespace App\Console\Commands\Blog;

use App\Contracts\Console\Commands\Blog\PublishScheduledPostsCommandInterface;
use App\Enums\PostStatusEnum;
use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    name        : 'blog:posts:publish',
    description : 'Publish scheduled blog posts for the current date'
)]
final class PublishScheduledPostsCommand extends Command implements PublishScheduledPostsCommandInterface
{
    private bool $showOutput = false;

    /** @var array<string, string> */
    private array $messages = [];

    public function __construct()
    {
        $this->setMessages();

        parent::__construct();
    }

    public function handle(): int
    {
        $this->showOutput = (bool) $this->option(key: 'debug');

        $this->displayMessage(
            message: $this->messages['checking']
        );

        $posts = $this->getScheduledPostsForToday();

        return $posts->isEmpty()
            ? $this->noPostsToProcess()
            : $this->processPosts(posts: $posts);
    }

    protected function configure(): void
    {
        $this->addOption(
            name       : 'debug',
            description: 'Show debug output'
        );
    }

    private function noPostsToProcess(): int
    {
        $this->displayMessage(
            message: $this->messages['noneFound']
        );

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, BlogPost>  $posts
     */
    private function processPosts(Collection $posts): int
    {
        $this->displayMessage(message: trans(key: $this->messages['countFound'], replace: [
            'count' => $posts->count(),
        ]));

        /** @var Collection<int, BlogPost> $posts */
        $posts->each(callback: fn (BlogPost $post) => $this->publishPost(post: $post));

        return self::SUCCESS;
    }

    /** @return Collection<int, BlogPost> */
    private function getScheduledPostsForToday(): Collection
    {
        return BlogPost::query()->where(
            column  : 'status',
            operator: '=',
            value   : PostStatusEnum::SCHEDULED
        )->whereDate(
            column  : 'scheduled_at',
            operator: '=',
            value   : now()->today()
        )->get();
    }

    private function publishPost(BlogPost $post): void
    {
        try
        {
            $this->displayMessage(message: trans(key: $this->messages['publishing'], replace: [
                'id'    => $post->id,
                'title' => $post->title,
            ]));

            $this->updatePost(post: $post);

            $this->displayMessage(message: trans(key: $this->messages['published'], replace: [
                'id'    => $post->id,
                'title' => $post->title,
            ]));
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            $this->displayMessage(
                message: $exception->getMessage(),
                type   : 'error'
            );
        }
    }

    /** @throws Throwable */
    private function updatePost(BlogPost $post): void
    {
        try
        {
            DB::transaction(callback: static function () use ($post): void
            {
                $post->updateQuietly(attributes: [
                    'status'       => PostStatusEnum::PUBLISHED,
                    'published_at' => now(),
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
            'checking'   => trans(key: 'blog.command.schedule.checking'),
            'noneFound'  => trans(key: 'blog.command.schedule.none.found'),
            'countFound' => trans(key: 'blog.command.schedule.count.found'),
            'publishing' => trans(key: 'blog.command.schedule.publishing'),
            'published'  => trans(key: 'blog.command.schedule.published'),
        ];
    }
}
