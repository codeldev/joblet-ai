<?php

/** @noinspection PhpExpressionResultUnusedInspection */

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Actions\Images\GenerateAction;
use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogImageNotBase64EncodedException;
use App\Exceptions\Blog\BlogPromptNotFoundDuringImageGenerationException;
use App\Facades\OpenAI;
use App\Models\BlogPost;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\Classes\Unit\Services\Blog\FakeOpenAIClient;
use Tests\Classes\Unit\Services\Blog\PayloadCapture;
use Tests\Classes\Unit\Services\Blog\PayloadCaptureOpenAIClient;

beforeEach(closure: function (): void
{
    $this->promptDisk = Mockery::mock(Filesystem::class);
    $this->imageDisk  = Mockery::mock(Filesystem::class);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_PROMPTS->value)
        ->andReturn($this->promptDisk);

    Storage::shouldReceive('disk')
        ->with(StorageDiskEnum::BLOG_IMAGES->value)
        ->andReturn($this->imageDisk);

    $this->imageDisk->shouldReceive(methodNames: 'put')
        ->zeroOrMoreTimes()
        ->andReturn(true);

    Config::set(key: 'blog.prompts.image', value: 'image.md');
    Config::set(key: 'blog.image.model', value: 'gpt-image-1');
    Config::set(key: 'blog.image.size', value: '1536x1024');
    Config::set(key: 'blog.image.quality', value: 'medium');
    Config::set(key: 'blog.image.format', value: 'png');
    Config::set(key: 'services.openai.api_key', value: 'test-api-key');

    // Use the actual implementation
    $this->app->bind(
        abstract: GenerateActionInterface::class,
        concrete: GenerateAction::class
    );

    OpenAI::fake();
    OpenAI::swap(instance: new FakeOpenAIClient());
});

afterEach(closure: function (): void
{
    Mockery::close();
});

describe(description: 'GenerateAction', tests: function (): void
{
    it('throws exception when image prompt file does not exist', function (): void
    {
        $this->promptDisk->shouldReceive(methodNames: 'exists')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $post = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PENDING_IMAGE,
        ]);

        Config::set(key: 'blog.prompts.image');

        $this->expectException(
            exception: BlogPromptNotFoundDuringImageGenerationException::class
        );

        $this->app->make(
            abstract  : GenerateActionInterface::class,
            parameters: ['postId' => $post->id]
        )->handle(
            tempPath    : $post->featuredImagePath(file: 'original.png'),
            promptString: $post->prompt->image_prompt
        );
    });

    it('throws exception when prompt file is empty', function (): void
    {
        $this->promptDisk->shouldReceive(methodNames: 'exists')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        $this->promptDisk->shouldReceive(methodNames: 'get')
            ->zeroOrMoreTimes()
            ->andReturn('');

        $post = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PENDING_IMAGE,
        ]);

        $this->expectException(
            exception: BlogPromptNotFoundDuringImageGenerationException::class
        );

        $this->app->make(
            abstract  : GenerateActionInterface::class,
            parameters: ['postId' => $post->id]
        )->handle(
            tempPath    : $post->featuredImagePath(file: 'original.png'),
            promptString: $post->prompt->image_prompt
        );
    });

    it('verifies payload is built correctly with proper dimensions and quality', function (): void
    {
        $this->promptDisk->shouldReceive(methodNames: 'exists')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        $this->promptDisk->shouldReceive(methodNames: 'get')
            ->zeroOrMoreTimes()
            ->andReturn('Generate an image: {{prompt}}');

        $post = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PENDING_IMAGE,
        ]);

        Config::set(key: 'blog.image.model', value: 'test-model');
        Config::set(key: 'blog.image.size', value: 'test-size');
        Config::set(key: 'blog.image.quality', value: 'test-quality');

        $payloadCapture = new PayloadCapture();

        OpenAI::swap(
            instance: new PayloadCaptureOpenAIClient(payloadCapture: $payloadCapture)
        );

        $this->app->make(
            abstract  : GenerateActionInterface::class,
            parameters: ['postId' => $post->id]
        )->handle(
            tempPath    : $post->featuredImagePath(file: 'original.png'),
            promptString: $post->prompt->image_prompt
        );

        expect(value: $payloadCapture->payload)
            ->toBeArray()
            ->toHaveKey(key: 'model', value: 'test-model')
            ->toHaveKey(key: 'size', value: 'test-size')
            ->toHaveKey(key: 'quality', value: 'test-quality')
            ->toHaveKey(key: 'prompt')
            ->toHaveKey(key: 'n', value: 1);
    });

    it('throws exception when OpenAI API key is not configured', function (): void
    {
        $this->promptDisk->shouldReceive(methodNames: 'exists')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        $this->promptDisk->shouldReceive(methodNames: 'get')
            ->zeroOrMoreTimes()
            ->andReturn('Generate an image: {{prompt}}');

        $post = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PENDING_IMAGE,
        ]);

        Config::set(key: 'services.openai.api_key', value: '');

        $this->expectException(
            exception: OpenAiApiKeyNotConfiguredException::class
        );

        $this->app->make(
            abstract  : GenerateActionInterface::class,
            parameters: ['postId' => $post->id]
        )->handle(
            tempPath    : $post->featuredImagePath(file: 'original.png'),
            promptString: $post->prompt->image_prompt
        );
    });

    it('throws an error if image is not a base64 encoded string', function (): void
    {
        $service = new GenerateAction;
        $method  = new ReflectionMethod(
            objectOrMethod: GenerateAction::class,
            method        : 'storeImageFile'
        );

        $method->setAccessible(accessible: true);

        $this->expectException(
            exception: BlogImageNotBase64EncodedException::class
        );

        $method->invoke(object: $service, base64Image: '!@#$%^&*()');
    });
});
