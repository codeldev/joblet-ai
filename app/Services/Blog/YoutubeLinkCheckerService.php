<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Alaouy\Youtube\Youtube;
use App\Models\BlogPost;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class YoutubeLinkCheckerService
{
    public function __construct(private Youtube $youtube) {}

    public function process(BlogPost $post): array
    {
        try
        {
            if (!$youtubeLinks = $this->extractYoutubeLinks(content: $post->content))
            {
                return $this->successResponse();
            }

            if ($removedLinks = $this->linksToRemove(youtubeLinks: $youtubeLinks))
            {
                $updatedContent = $this->removeLinks(
                    content: $post->content,
                    links  : $removedLinks
                );

                $this->saveUpdatedContent(
                    post   : $post,
                    content: $updatedContent
                );
            }

            return $this->successResponse(
                found  : count(value: $youtubeLinks),
                removed: count(value: $removedLinks)
            );
        }
        catch (Exception|Throwable $exception)
        {
            return $this->responseWithError(
                $exception->getMessage()
            );
        }
    }

    private function linksToRemove(array $youtubeLinks): array
    {
        return collect(value: $youtubeLinks)
            ->filter(callback: fn (string $link) => $this->shouldRemoveYoutubeLink(link: $link))
            ->values()
            ->toArray();
    }

    private function shouldRemoveYoutubeLink(string $link): bool
    {
        if (!$videoId = $this->extractVideoId(url: $link))
        {
            return true;
        }

        return !$this->isVideoAccessible(videoId: $videoId);
    }

    private function responseWithError(string $message): array
    {
        return [
            'success' => false,
            'found'   => 0,
            'removed' => 0,
            'error'   => $message,
        ];
    }

    private function successResponse(int $found = 0, int $removed = 0): array
    {
        return [
            'success' => true,
            'found'   => $found,
            'removed' => $removed,
        ];
    }

    private function extractYoutubeLinks(string $content): array
    {
        return collect(value: explode(separator: "\n", string: $content))
            ->map(callback: fn (string $line) => trim(string: $line))
            ->filter(callback: fn(string $line) => $this->filterYoutubeLinks(line: $line))
            ->values()
            ->toArray();
    }

    private function filterYoutubeLinks(string $line): bool
    {
        return collect(value: $this->getLinkPatterns())->contains(
            key: fn (string $pattern) => preg_match(pattern: $pattern, subject: $line) === 1
        );
    }

    private function getLinkPatterns(): array
    {
        return [
            '~^https?://(?:www\.)?youtube\.com/watch\?v=([a-zA-Z0-9_-]+)(?:&\S*)?$~',
            '~^https?://(?:www\.)?youtu\.be/([a-zA-Z0-9_-]+)(?:\?\S*)?$~',
            '~^https?://(?:www\.)?youtube\.com/embed/([a-zA-Z0-9_-]+)(?:\?\S*)?$~'
        ];
    }

    private function extractVideoId(string $url): ?string
    {
        $pattern = '~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([a-zA-Z0-9_-]+)~';

        if (preg_match(pattern: $pattern, subject: $url, matches: $matches))
        {
            return $matches[1];
        }

        return null;
    }

    private function isVideoAccessible(string $videoId): bool
    {
        try
        {
            $videoInfo = $this->youtube->getVideoInfo(vId: $videoId);

            return !empty($videoInfo) && isset($videoInfo->snippet);
        }
        catch (Exception)
        {
            return false;
        }
    }

    private function removeLinks(string $content, array $links): string
    {
        return collect(value: explode(separator: "\n", string: $content))->filter(
            callback: fn(string $line) => $this->shouldRemoveVideoLink(line: $line, links: $links)
        )->implode(value:  "\n");
    }

    private function shouldRemoveVideoLink(string $line, array $links): bool
    {
        return !in_array(
            needle  : trim(string: $line),
            haystack: $links,
            strict  : true
        );
    }

    /** @throws Throwable */
    private function saveUpdatedContent(BlogPost $post, string $content): void
    {
        DB::transaction(callback: static fn() => $post->updateQuietly(attributes: [
            'content' => $content,
        ]));
    }
}
