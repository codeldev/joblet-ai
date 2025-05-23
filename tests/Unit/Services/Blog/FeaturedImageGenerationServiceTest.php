<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Contracts\Services\Blog\FeaturedImageGenerationServiceInterface;
use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Exceptions\Blog\BlogPostImageAlreadyGeneratedException;
use App\Exceptions\Blog\BlogPostNotFoundDuringImageGenerationException;
use App\Exceptions\Blog\BlogPostNotPendingImageStatusException;
use App\Exceptions\Blog\BlogPromptImagePromptMissingException;
use App\Jobs\ProcessBlogContentImagesJob;
use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Models\BlogPrompt;
use App\Services\Blog\FeaturedImageGenerationService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

beforeEach(closure: function (): void
{
    Bus::fake();
});

test(description: 'service implements expected interface', closure: function (): void
{
    expect(value: FeaturedImageGenerationService::class)
        ->toImplement(interfaces: FeaturedImageGenerationServiceInterface::class);
});

test(description: 'service throws exception when blog post not found', closure: function (): void
{
    $this->expectException(
        exception: BlogPostNotFoundDuringImageGenerationException::class
    );

    new FeaturedImageGenerationService(
        postId: 'non-existent-id'
    );
});

test(description: 'service throws exception when blog post is not in pending image status', closure: function (): void
{
    $blogPost = BlogPost::factory()->create(attributes: [
        'status' => PostStatusEnum::DRAFT,
    ]);

    $this->expectException(
        exception: BlogPostNotPendingImageStatusException::class
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service throws exception when blog post already has featured image', closure: function (): void
{
    $blogPost = BlogPost::factory()->create(attributes: [
        'status' => PostStatusEnum::PENDING_IMAGE,
    ]);

    BlogImage::factory()->create(attributes: [
        'post_id' => $blogPost->id,
        'type'    => BlogImageTypeEnum::FEATURED,
    ]);

    $this->expectException(
        exception: BlogPostImageAlreadyGeneratedException::class
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service throws exception when blog prompt has no image prompt', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => '',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->expectException(
        exception: BlogPromptImagePromptMissingException::class
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service generates and stores featured image successfully', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock) use ($blogPrompt): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->withArgs(
                argsOrClosure: fn (string $tempPath, string $promptString): bool => $promptString === $blogPrompt->image_prompt
            );
        }
    );

    $this->mock(
        abstract: ResizeActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->withArgs(
                argsOrClosure: fn (string $sourceFile, string $destination, StorageDiskEnum $storageDisk): bool => $storageDisk === StorageDiskEnum::BLOG_IMAGES
            )->andReturn([
                ['width' => 400, 'image' => '400w.webp'],
                ['width' => 700, 'image' => '700w.webp'],
                ['width' => 1000, 'image' => '1000w.webp'],
            ]);
        }
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();

    $blogPost->refresh();

    expect(value: $blogPost->has_featured_image)
        ->toBeTrue()
        ->and(value: $featuredImage = $blogPost->featured)
        ->not()->toBeNull()
        ->and(value: $featuredImage->type)
        ->toBe(expected: BlogImageTypeEnum::FEATURED)
        ->and(value: $featuredImage->description)
        ->toBe(expected: $blogPrompt->image_prompt)
        ->and(value: $featuredImage->files)
        ->toBeInstanceOf(class: ArrayObject::class);

    Bus::assertDispatched(
        command: ProcessBlogContentImagesJob::class,
        callback: fn (ProcessBlogContentImagesJob $job): bool => $job->postId === $blogPost->id
    );
});

test(description: 'service uses correct image format from config', closure: function (): void
{
    Config::set('blog.image.format', 'jpg');

    $imagePath  = '';
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock    : function (MockInterface $mock) use (&$imagePath): void
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
        mock    : function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once()->andReturn([
                ['width' => 400, 'image' => '400w.webp'],
            ]);
        }
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();

    expect(value: $imagePath)
        ->toContain(needles: 'original.jpg');
});

test(description: 'service handles database transaction errors during image size storage', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once();
        }
    );

    DB::partialMock()
        ->shouldReceive(methodNames: 'transaction')
        ->withAnyArgs()
        ->once()
        ->andThrow(exception: new RuntimeException(message: 'Database error'));

    $this->expectException(
        exception: RuntimeException::class
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();
});

test(description: 'service handles image generation errors', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
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
        exception: RuntimeException::class
    );

    new FeaturedImageGenerationService(postId: $blogPost->id)
        ->handle();

    Bus::assertNotDispatched(
        command: ProcessBlogContentImagesJob::class
    );
});

test(description: 'service throws exception during image size storage (lines 100-104)', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $service = new FeaturedImageGenerationService(postId: $blogPost->id);

    DB::partialMock()
        ->shouldReceive(methodNames: 'transaction')
        ->withAnyArgs()
        ->once()
        ->andThrow(exception: new RuntimeException(message: 'Database error during image size storage'));

    $this->mock(
        abstract: GenerateActionInterface::class,
        mock: function (MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'handle')->once();
        }
    );

    $this->expectException(
        exception: RuntimeException::class
    );

    $service->handle();
});

test(description: 'service handles exceptions in storeImageSizes method (lines 100-104)', closure: function (): void
{
    $blogPrompt = BlogPrompt::factory()->create(attributes: [
        'image_prompt' => 'A beautiful landscape with mountains and lakes',
    ]);

    $blogPost = BlogPost::factory()->create(attributes: [
        'status'    => PostStatusEnum::PENDING_IMAGE,
        'prompt_id' => $blogPrompt->id,
    ]);

    $service    = new FeaturedImageGenerationService(postId: $blogPost->id);
    $reflection = new ReflectionClass(objectOrClass: $service);
    $method     = $reflection->getMethod(name: 'storeImageSizes');

    $method->setAccessible(accessible: true);

    DB::partialMock()
        ->shouldReceive(methodNames: 'transaction')
        ->withAnyArgs()
        ->once()
        ->andThrow(exception: new RuntimeException(message: 'Database error during image size storage'));

    $this->expectException(
        exception: RuntimeException::class
    );

    $method->invoke($service, [
        ['width' => 400, 'image' => '400w.webp'],
    ]);
});
