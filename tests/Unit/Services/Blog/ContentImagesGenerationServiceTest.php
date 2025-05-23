<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Contracts\Services\Blog\ContentImagesGenerationServiceInterface;
use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostNotFoundDuringImageGenerationException;
use App\Exceptions\Blog\BlogPostNotPendingImageStatusException;
use App\Models\BlogPost;
use App\Models\BlogPrompt;
use App\Services\Blog\ContentImagesGenerationService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

test(description: 'service implements expected interface', closure: function (): void
{
    expect(value: ContentImagesGenerationService::class)
        ->toImplement(interfaces: ContentImagesGenerationServiceInterface::class);
});

test(description: 'service throws exception when blog post not found', closure: function (): void
{
    $this->expectException(
        BlogPostNotFoundDuringImageGenerationException::class
    );

    new ContentImagesGenerationService(
        postId: 'non-existent-id'
    );
});

test(description: 'service throws exception when blog post is not in pending image status', closure: function (): void
{
    $blogPost = BlogPost::factory()->create(attributes: [
        'status' => PostStatusEnum::DRAFT,
    ]);

    $this->expectException(
        BlogPostNotPendingImageStatusException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service throws exception when OpenAI API key is not configured', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => ['image1' => 'A beautiful landscape with mountains and lakes'],
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    Config::set('services.openai.api_key');

    $this->expectException(
        OpenAiApiKeyNotConfiguredException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service does not generate images when content_images is empty', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => null,
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock    : function (MockInterface $mock): void
        {
            $mock->shouldNotReceive(methodNames: 'handle');
        }
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();

    $blogPost->refresh();

    expect(value: $blogPost->status)
        ->toBe(expected: PostStatusEnum::SCHEDULED);
});

test(description: 'service generates and stores content images successfully', closure: function (): void
{
    $contentImages = [
        'image1' => 'A beautiful landscape with mountains and lakes',
        'image2' => 'A cityscape at night with bright lights',
    ];

    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => $contentImages,
    ]);

    $content = 'This is a blog post with image1 and image2 placeholders.';

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
        'content'   => $content,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock) use ($contentImages): void
        {
            $mock->shouldReceive(methodNames: 'handle')
                ->times(limit: count(value: $contentImages))
                ->withArgs(
                    argsOrClosure: fn (string $tempPath, string $promptString): bool => in_array(needle: $promptString, haystack: $contentImages, strict: true)
                );
        }
    );

    $this->mock(
        abstract: ResizeActionInterface::class,
        mock: function (MockInterface $mock) use ($contentImages): void
        {
            $mock->shouldReceive(methodNames: 'handle')
                ->times(limit: count(value: $contentImages))
                ->withArgs(
                    argsOrClosure: fn (string $sourceFile, string $destination, StorageDiskEnum $storageDisk): bool => $storageDisk === StorageDiskEnum::BLOG_IMAGES
                )
                ->andReturn([
                    ['width' => 400, 'image' => '400w.webp'],
                    ['width' => 700, 'image' => '700w.webp'],
                    ['width' => 1000, 'image' => '1000w.webp'],
                ]);
        }
    );

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'exists')->andReturn(false);
    $diskMock->shouldReceive(methodNames: 'makeDirectory')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'get')->andReturn('fake-image-content');
    $diskMock->shouldReceive(methodNames: 'put')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();

    $blogPost->refresh();

    expect(value: $blogPost->status)
        ->toBe(expected: PostStatusEnum::SCHEDULED)
        ->and(value: $blogPost->images()->count())
        ->toBe(expected: count(value: $contentImages));

    $contentImagesCount = $blogPost->images()
        ->where('type', '=', BlogImageTypeEnum::CONTENT)
        ->count();

    expect(value: $contentImagesCount)
        ->toBe(expected: count(value: $contentImages));

    foreach ($contentImages as $prompt)
    {
        $image = $blogPost->images()
            ->where('type', '=', BlogImageTypeEnum::CONTENT)
            ->where('description', '=', $prompt)
            ->first();

        expect(value: $image)
            ->not()->toBeNull()
            ->and(value: $blogPost->content)
            ->toContain(needles: "img={$image->id}");
    }
});

test(description: 'service uses correct image format from config', closure: function (): void
{
    Config::set('blog.image.format', value: 'jpg');

    $imagePath  = '';
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => ['image1' => 'A beautiful landscape with mountains and lakes'],
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock) use (&$imagePath): void
        {
            $mock->shouldReceive(methodNames: 'handle')
                ->once()
                ->withArgs(argsOrClosure: function (string $tempPath) use (&$imagePath): bool
                {
                    $imagePath = $tempPath;

                    return true;
                });
        }
    );

    $this->mock(
        abstract: ResizeActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->andReturn([
                ['width' => 400, 'image' => '400w.webp'],
            ]);
        }
    );

    // Mock the disk operations
    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'exists')->andReturn(false);
    $diskMock->shouldReceive(methodNames: 'makeDirectory')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'get')->andReturn('fake-image-content');
    $diskMock->shouldReceive(methodNames: 'put')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();

    expect(value: $imagePath)
        ->toContain(needles: 'original.jpg');
});

test(description: 'service handles database transaction errors during image processing', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => ['image1' => 'A beautiful landscape with mountains and lakes'],
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    DB::partialMock()
        ->shouldReceive(methodNames: 'transaction')
        ->withAnyArgs()
        ->once()
        ->andThrow(exception: new RuntimeException(message: 'Database error'));

    $this->expectException(
        RuntimeException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service handles image generation errors', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => ['image1' => 'A beautiful landscape with mountains and lakes'],
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->andThrow(
                exception: new RuntimeException(message: 'Image generation failed')
            );
        }
    );

    $this->expectException(
        RuntimeException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service handles exceptions in processBlogPostContent method', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => ['image1' => 'A beautiful landscape with mountains and lakes'],
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
        'content'   => 'Content with image1 placeholder',
    ]);

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'exists')->andReturn(false);
    $diskMock->shouldReceive(methodNames: 'makeDirectory')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'get')->andReturn('fake-image-content');
    $diskMock->shouldReceive(methodNames: 'put')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    // Mock GenerateAction and ResizeAction
    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once();
        }
    );

    $this->mock(
        abstract: ResizeActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->andReturn([
                ['width' => 400, 'image' => '400w.webp'],
            ]);
        }
    );

    DB::shouldReceive('transaction')
        ->zeroOrMoreTimes()
        ->andReturnUsing(function ($callback)
        {
            static $callCount = 0;

            $callCount++;

            if ($callCount === 1)
            {
                return $callback();
            }

            throw new RuntimeException(
                message: 'Database error during content update'
            );
        });

    $this->expectException(
        RuntimeException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service handles exceptions in schedulePost method', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => null,
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    DB::shouldReceive('transaction')->once()->andThrow(
        exception: new RuntimeException(message: 'Database error during scheduling')
    );

    $this->expectException(
        RuntimeException::class
    );

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service correctly replaces image keys in content', closure: function (): void
{
    $contentImages = [
        'image1' => 'A beautiful landscape with mountains and lakes',
        'image2' => 'A cityscape at night with bright lights',
    ];

    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => $contentImages,
    ]);

    $content = 'This is a blog post with image1 and image2 placeholders.';

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
        'content'   => $content,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->twice();
        }
    );

    $this->mock(
        abstract: ResizeActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->twice()->andReturn([
                ['width' => 400, 'image' => '400w.webp'],
            ]);
        }
    );

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'exists')->andReturn(false);
    $diskMock->shouldReceive(methodNames: 'makeDirectory')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'get')->andReturn('fake-image-content');
    $diskMock->shouldReceive(methodNames: 'put')->andReturn(true);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')->andReturn(true);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();

    $blogPost->refresh();

    foreach ($contentImages as $key => $prompt)
    {
        $image = $blogPost->images()
            ->where('type', '=', BlogImageTypeEnum::CONTENT)
            ->where('description', '=', $prompt)
            ->first();

        expect(value: $blogPost->content)
            ->toContain(needles: "img={$image->id}")
            ->not()->toContain(needles: $key);
    }
});

test(description: 'service handles temp directory deletion errors gracefully', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'content_images' => null,
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $diskMock = Mockery::mock(args: Filesystem::class);
    $diskMock->shouldReceive(methodNames: 'deleteDirectory')
        ->andThrow(exception: new RuntimeException(message: 'Error deleting directory'));

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($diskMock);

    new ContentImagesGenerationService(postId: $blogPost->id)
        ->handle();

    $blogPost->refresh();

    expect(value: $blogPost->status)
        ->toBe(expected: PostStatusEnum::SCHEDULED);
});
