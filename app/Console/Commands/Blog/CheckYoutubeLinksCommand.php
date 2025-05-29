<?php

declare(strict_types=1);

namespace App\Console\Commands\Blog;

use App\Concerns\HasCommandsTrait;
use App\Models\BlogPost;
use App\Services\Blog\YoutubeLinkCheckerService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

#[AsCommand(
    name: 'blog:posts:youtube',
    description: 'Check and remove invalid YouTube links from blog posts.'
)]
final class CheckYoutubeLinksCommand extends Command
{
    use HasCommandsTrait;

    public function __construct(private readonly YoutubeLinkCheckerService $youtubeChecker)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $posts = $this->getAvailablePosts();

        if ($posts->isEmpty())
        {
            return $this->noPostsFound();
        }

        if($this->isRunningFromScheduler())
        {
            return $this->processAllPosts(posts: $posts);
        }

        $processAll = confirm(
            label  : 'Would you like to process all blog posts?',
            default: false
        );

        return $processAll
            ? $this->processAllPosts(posts: $posts)
            : $this->requestPostSelection(posts: $posts);
    }

    private function getAvailablePosts(): Collection
    {
        return BlogPost::query()
            ->orderBy(column: 'title')
            ->get(['id', 'title', 'content']);
    }

    private function postsForSelection(Collection $posts): array
    {
        return $posts->mapWithKeys(
            callback: fn ($post) => [$post->id => 'ID: ' . $post->id . ' | ' . $post->title]
        )->toArray();
    }

    private function requestPostSelection(Collection $posts): int
    {
        $postId = select(
            label  : 'Which blog post would you like to check for YouTube links?',
            options: $this->postsForSelection(posts: $posts),
            scroll : 10
        );

        return !$postId
            ? $this->noPostSelected()
            : $this->processPost(post: BlogPost::find(id: $postId));
    }

    private function processAllPosts(Collection $posts): int
    {
        $posts->each(
            callback: fn (BlogPost $post) => $this->processPost(post: $post)
        );

        return self::SUCCESS;
    }

    private function noPostsFound(): int
    {
        $this->outputErrorMessage(
            message: 'No blog posts found.'
        );

        return self::SUCCESS;
    }

    private function noPostSelected(): int
    {
        $this->outputErrorMessage(
            message: 'No blog post selected.'
        );

        return self::FAILURE;
    }

    private function processPost(BlogPost $post): int
    {
        try
        {
            $result = $this->youtubeChecker->process(post: $post);

            return $result['success']
                ? $this->processingPostSuccess(result: $result)
                : $this->processingPostError(error: $result['error'] ?? 'Unknown error');
        }
        catch (Exception|Throwable $exception)
        {
            return $this->processingPostError(
                error: $exception->getMessage()
            );
        }
    }

    private function processingPostError(string $error): int
    {
        $this->outputErrorMessage(
            message: "Error processing post: {$error}."
        );

        return self::FAILURE;
    }

    private function processingPostSuccess(array $result): int
    {
        $this->outputInfoMessage(
            message: "Found {$result['found']} YouTube links, removed {$result['removed']} invalid links."
        );

        return self::SUCCESS;
    }
}
