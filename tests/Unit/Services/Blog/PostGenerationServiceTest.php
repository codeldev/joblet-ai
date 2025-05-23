<?php

/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\AiProviders\Anthropic\AnthropicInterface;
use App\Contracts\Services\AiProviders\OpenAI\ChatGptInterface;
use App\Contracts\Services\Blog\PostGenerationServiceInterface;
use App\Enums\PostStatusEnum;
use App\Exceptions\Blog\BlogContentMissingImagePromptException;
use App\Exceptions\Blog\BlogContentNotArrayException;
use App\Exceptions\Blog\BlogIdeaAlreadyProcessedException;
use App\Exceptions\Blog\BlogIdeaNotFoundDuringQueuedJobException;
use App\Jobs\ProcessBlogFeaturedImageJob;
use App\Models\BlogIdea;
use App\Models\BlogPrompt;
use App\Services\Blog\PostGenerationService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

beforeEach(closure: function (): void
{
    $this->blogIdea = BlogIdea::factory()->create(attributes: [
        'topic'         => 'Test Topic',
        'keywords'      => 'test, php, laravel',
        'focus'         => 'Test focus',
        'requirements'  => 'Test requirements',
        'additional'    => 'Test additional info',
        'schedule_date' => now()->addDay(),
    ]);
});

describe(description: 'PostGenerationService', tests: function (): void
{
    it(description: 'implements post generation service interface', closure: function (): void
    {
        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        expect(value: $service)->toBeInstanceOf(
            class: PostGenerationServiceInterface::class
        );
    });

    it(description: 'throws exception when idea not found', closure: function (): void
    {
        $service = new PostGenerationService(
            ideaId: Str::uuid()->toString()
        );

        $this->expectException(
            exception: BlogIdeaNotFoundDuringQueuedJobException::class
        );

        $service->handle(aiProvider: 'openAI');
    });

    it(description: 'throws exception when idea already processed', closure: function (): void
    {
        $this->blogIdea->updateQuietly(attributes: [
            'processed_at' => now(),
        ]);

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: BlogIdeaAlreadyProcessedException::class
        );

        $service->handle(aiProvider: 'openAI');
    });

    it(description: 'throws exception when content is not an array', closure: function (): void
    {
        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn('not-an-array');

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(exception: TypeError::class);

        $service->handle(aiProvider: 'openAI');
    });

    it(description: 'throws exception when required keys are missing', closure: function (): void
    {
        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn([
                'meta_title'       => 'Test',
                'meta_description' => 'Test',
                'post_summary'     => 'Test',
                'post_content'     => 'Test',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: BlogContentMissingImagePromptException::class
        );

        $service->handle(aiProvider: 'openAI');
    });

    it(description: 'creates blog post with valid content', closure: function (): void
    {
        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn($validContent = [
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->once()
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->once()
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle(aiProvider: 'openAI');

        $this->assertDatabaseHas(table: 'blog_posts', data: [
            'title'       => $validContent['meta_title'],
            'description' => $validContent['meta_description'],
            'summary'     => $validContent['post_summary'],
            'content'     => $validContent['post_content'],
            'status'      => PostStatusEnum::PENDING_IMAGE->value,
        ]);

        $this->assertDatabaseHas(table: 'blog_prompts', data: [
            'meta_title'       => $validContent['meta_title'],
            'meta_description' => $validContent['meta_description'],
            'post_content'     => $validContent['post_content'],
            'post_summary'     => $validContent['post_summary'],
            'image_prompt'     => $validContent['image_prompt'],
        ]);

        expect(value: $this->blogIdea->fresh()->processed_at)
            ->not->toBeNull();

        Bus::assertDispatched(
            command: ProcessBlogFeaturedImageJob::class
        );
    });

    it(description: 'rolls back transaction on failure', closure: function (): void
    {
        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn([
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->once()
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->once()
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(exception: new RuntimeException(message: 'Database error'));

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: RuntimeException::class
        );

        $service->handle(aiProvider: 'openAI');

        $this->assertDatabaseCount(table: 'blog_posts', count: 0);
        $this->assertDatabaseCount(table: 'blog_prompts', count: 0);

        expect(value: $this->blogIdea->fresh()->processed_at)
            ->toBeNull();
    });

    it(description: 'handles throwable exceptions', closure: function (): void
    {
        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andThrow(exception: new Error(message: 'Test error'));

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: Error::class
        );

        $service->handle(aiProvider: 'openAI');
    });

    it(description: 'uses default delay when config is not numeric', closure: function (): void
    {
        config(key: ['blog.image.delay' => 'invalid']);

        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn([
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle(aiProvider: 'openAI');

        Bus::assertDispatched(
            command : ProcessBlogFeaturedImageJob::class,
            callback: function ($job)
            {
                $this->assertEqualsWithDelta(
                    now()->addMinutes(value: 15)->timestamp,
                    $job->delay->timestamp,
                    1
                );

                return true;
            }
        );
    });

    it(description: 'handles non-array content in validateResponse', closure: function (): void
    {
        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: BlogContentNotArrayException::class
        );

        $reflection = new ReflectionClass(objectOrClass: $service);
        $method     = $reflection->getMethod(name: 'validateResponse');

        $method->setAccessible(accessible: true);
        $method->invokeArgs($service, ['not-an-array']);
    });

    it(description: 'handles openAI provider in match statement', closure: function (): void
    {
        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn($validContent = [
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle(aiProvider: 'openAI');

        $this->assertDatabaseHas(table: 'blog_posts', data: [
            'title' => $validContent['meta_title'],
        ]);
    });

    it(description: 'handles anthropic provider in match statement', closure: function (): void
    {
        $mockAnthropic = mock(args: AnthropicInterface::class);
        $mockAnthropic->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn($validContent = [
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockAnthropic->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockAnthropic->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: AnthropicInterface::class,
            instance: $mockAnthropic
        );

        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle();

        $this->assertDatabaseHas(table: 'blog_posts', data: [
            'title' => $validContent['meta_title'],
        ]);
    });

    it(description: 'throws exception for unsupported provider', closure: function (): void
    {
        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $this->expectException(
            exception: InvalidArgumentException::class
        );

        $this->expectExceptionMessage('Unsupported AI provider: unsupported');

        $service->handle(aiProvider: 'unsupported');
    });

    it(description: 'handles content with content_images JSON string', closure: function (): void
    {
        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $contentImagesJson = json_encode(value: [
            ['id' => 'img1', 'prompt' => 'Test image 1'],
            ['id' => 'img2', 'prompt' => 'Test image 2'],
        ], flags: JSON_THROW_ON_ERROR);

        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn([
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
                'content_images'   => $contentImagesJson,
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle(aiProvider: 'openAI');

        $this->assertDatabaseHas(table: 'blog_prompts', data: [
            'meta_title'       => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ]);

        $prompt = BlogPrompt::query()->where(
            column   : 'meta_title',
            operator : '=',
            value    : 'Test Meta Title'
        )->first();

        expect(value: $prompt)
            ->not->toBeNull()
            ->and(value: json_encode(value: $prompt->content_images))
            ->toContain(needles: 'img1')
            ->toContain(needles: 'img2');
    });

    it(description: 'handles content without content_images key', closure: function (): void
    {
        Bus::fake(jobsToFake: [ProcessBlogFeaturedImageJob::class]);

        $mockChatGpt = mock(args: ChatGptInterface::class);
        $mockChatGpt->shouldReceive(methodNames: 'handle')
            ->once()
            ->andReturn([
                'meta_title'       => 'Test Meta Title',
                'meta_description' => 'Test Meta Description',
                'post_summary'     => 'Test Post Summary',
                'post_content'     => 'Test Post Content',
                'image_prompt'     => 'Test Image Prompt',
            ]);

        $mockChatGpt->shouldReceive(methodNames: 'getUserPrompt')
            ->andReturn('Test User Prompt');

        $mockChatGpt->shouldReceive(methodNames: 'getSystemPrompt')
            ->andReturn('Test System Prompt');

        $this->app->instance(
            abstract: ChatGptInterface::class,
            instance: $mockChatGpt
        );

        $service = new PostGenerationService(
            ideaId: $this->blogIdea->id
        );

        $service->handle(aiProvider: 'openAI');

        $this->assertDatabaseHas(table: 'blog_prompts', data: [
            'meta_title'       => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ]);

        $prompt = BlogPrompt::query()->where(
            column   : 'meta_title',
            operator : '=',
            value    : 'Test Meta Title'
        )->first();

        expect(value: $prompt)
            ->not->toBeNull()
            ->and(value: $prompt->content_images)
            ->toBeEmpty();
    });
});
