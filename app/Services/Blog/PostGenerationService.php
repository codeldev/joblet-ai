<?php

declare(strict_types=1);

namespace App\Services\Blog;

use App\Contracts\Services\AiProviders\Anthropic\AnthropicInterface;
use App\Contracts\Services\AiProviders\OpenAI\ChatGptInterface;
use App\Contracts\Services\Blog\PostGenerationServiceInterface;
use App\Enums\PostStatusEnum;
use App\Exceptions\Blog\BlogContentMissingImagePromptException;
use App\Exceptions\Blog\BlogContentMissingMetaDescriptionException;
use App\Exceptions\Blog\BlogContentMissingMetaTitleException;
use App\Exceptions\Blog\BlogContentMissingPostContentException;
use App\Exceptions\Blog\BlogContentMissingPostSummaryException;
use App\Exceptions\Blog\BlogContentNotArrayException;
use App\Exceptions\Blog\BlogIdeaAlreadyProcessedException;
use App\Exceptions\Blog\BlogIdeaNotFoundDuringQueuedJobException;
use App\Jobs\ProcessBlogFeaturedImageJob;
use App\Models\BlogIdea;
use App\Models\BlogPost;
use App\Models\BlogPrompt;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;
use Throwable;

final class PostGenerationService implements PostGenerationServiceInterface
{
    private readonly ?BlogIdea $idea;

    private string $userPrompt = '';

    private string $systemPrompt = '';

    public function __construct(private readonly string $ideaId)
    {
        $this->idea = BlogIdea::find(id: $this->ideaId);
    }

    /**
     * @throws JsonException
     * @throws Throwable
     * @throws BlogContentNotArrayException
     * @throws BlogContentMissingImagePromptException
     * @throws BlogContentMissingPostContentException
     * @throws BlogContentMissingMetaDescriptionException
     * @throws BlogContentMissingPostSummaryException
     * @throws BlogContentMissingMetaTitleException
     * @throws BlogIdeaNotFoundDuringQueuedJobException
     * @throws BlogIdeaAlreadyProcessedException
     */
    public function handle(string $aiProvider = 'anthropic'): void
    {
        if (! ($this->idea instanceof BlogIdea))
        {
            throwException(
                exceptionClass: BlogIdeaNotFoundDuringQueuedJobException::class
            );
        }

        /** @var BlogIdea $idea */
        $idea = $this->idea;

        if ($idea->processed_at !== null)
        {
            throwException(
                exceptionClass: BlogIdeaAlreadyProcessedException::class
            );
        }

        $content = $this->generateBlogContent(provider: $aiProvider);

        $this->validateResponse(content: $content);

        $this->createPost(content: $content);
    }

    /**
     * @param  array<string,string>  $content
     *
     * @throws Throwable
     */
    private function createPost(array $content): void
    {
        try
        {
            DB::transaction(callback: function () use ($content): void
            {
                $prompt = BlogPrompt::create(attributes: [
                    'meta_title'       => $content['meta_title'],
                    'meta_description' => $content['meta_description'],
                    'post_content'     => $content['post_content'],
                    'post_summary'     => $content['post_summary'],
                    'image_prompt'     => $content['image_prompt'],
                    'system_prompt'    => $this->systemPrompt,
                    'user_prompt'      => $this->userPrompt,
                    'content_images'   => array_key_exists(key: 'content_images', array: $content)
                        ? json_decode(
                            json       : $content['content_images'],
                            associative: false,
                            depth      : 500,
                            flags      : JSON_THROW_ON_ERROR
                        )
                        : [],
                ]);

                /** @var BlogIdea $idea */
                $idea  = $this->idea;
                $text  = $content['meta_title'] . ' ' . $content['post_content'];

                /** @var int $words */
                $words = str_word_count(string: $text);

                $post = $idea->post()->create(attributes: [
                    'prompt_id'    => $prompt->id,
                    'title'        => $content['meta_title'],
                    'description'  => $content['meta_description'],
                    'summary'      => $content['post_summary'],
                    'content'      => $content['post_content'],
                    'status'       => PostStatusEnum::PENDING_IMAGE,
                    'scheduled_at' => $idea->schedule_date,
                    'word_count'   => $words,
                    'read_time'    => ceil(num: $words / 180),
                ]);

                $idea->updateQuietly(attributes: [
                    'processed_at' => now(),
                ]);

                $this->queueImageGenerationJob(post: $post);
            });
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    private function queueImageGenerationJob(BlogPost $post): void
    {
        $default = 15;
        $config  = config(key: 'blog.image.delay', default: $default);
        $delay   = is_numeric(value: $config)
            ? (int) $config
            : $default;

        ProcessBlogFeaturedImageJob::dispatch(postId: $post->id)
            ->delay(delay: Carbon::now()->addMinutes(value: $delay));
    }

    /**
     * @return array<string,string>
     *
     * @throws JsonException
     * @throws Throwable
     */
    private function generateBlogContent(string $provider): array
    {
        try
        {
            $aiProvider = match ($provider)
            {
                'anthropic' => app()->make(abstract: AnthropicInterface::class),
                'openAI'    => app()->make(abstract: ChatGptInterface::class),
                default     => throw new InvalidArgumentException(message: "Unsupported AI provider: {$provider}"),
            };

            /** @var BlogIdea $idea */
            $idea = $this->idea;

            /** @var array<string,string> $content */
            $content = $aiProvider->handle(blogIdea: $idea);

            $this->userPrompt   = $aiProvider->getUserPrompt();
            $this->systemPrompt = $aiProvider->getSystemPrompt();

            return $content;
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception */
    private function validateResponse(mixed $content): void
    {
        if (! is_array(value: $content))
        {
            throwException(exceptionClass: BlogContentNotArrayException::class);
        }

        /** @var array<string, mixed> $contentArray */
        $contentArray = $content;

        foreach ($this->requiredKeys() as $key => $exception)
        {
            if (! array_key_exists(key: $key, array: $contentArray))
            {
                throwException(exceptionClass: $exception);
            }
        }
    }

    /** @return array<string, class-string<Exception>> */
    private function requiredKeys(): array
    {
        return [
            'meta_title'       => BlogContentMissingMetaTitleException::class,
            'meta_description' => BlogContentMissingMetaDescriptionException::class,
            'post_summary'     => BlogContentMissingPostSummaryException::class,
            'post_content'     => BlogContentMissingPostContentException::class,
            'image_prompt'     => BlogContentMissingImagePromptException::class,
        ];
    }
}
