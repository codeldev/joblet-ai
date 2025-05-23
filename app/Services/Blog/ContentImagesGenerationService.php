<?php

declare(strict_types=1);

namespace App\Services\Blog;

use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Contracts\Services\Blog\ContentImagesGenerationServiceInterface;
use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostNotFoundDuringImageGenerationException;
use App\Exceptions\Blog\BlogPostNotPendingImageStatusException;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class ContentImagesGenerationService implements ContentImagesGenerationServiceInterface
{
    private readonly BlogPost $blogPost;

    /** @var array<string, string> */
    private array $imageKeys;

    /** @throws Exception */
    public function __construct(private readonly string $postId)
    {
        $blogPost = $this->findBlogPost();

        if (! ($blogPost instanceof BlogPost))
        {
            throwException(
                exceptionClass: BlogPostNotFoundDuringImageGenerationException::class
            );
        }

        /** @var BlogPost $blogPost */
        $this->blogPost  = $blogPost;
        $this->imageKeys = [];
    }

    /** @throws Exception|Throwable */
    public function handle(): void
    {
        $this->deleteTempDirectory();

        if (notEmpty(value: $this->blogPost->prompt->content_images))
        {
            $this->runRequirementChecks();
            $this->generateContentImages();
        }

        $this->schedulePost();
    }

    /** @throws Exception */
    private function runRequirementChecks(): void
    {
        $this->validatePostStatus();
        $this->validateApiKey();
    }

    /** @throws Exception */
    private function validatePostStatus(): void
    {
        /** @var PostStatusEnum $status */
        $status = $this->blogPost->status;

        if ($status->value !== PostStatusEnum::PENDING_IMAGE->value)
        {
            throwException(
                exceptionClass: BlogPostNotPendingImageStatusException::class
            );
        }
    }

    /** @throws Exception */
    private function validateApiKey(): void
    {
        if (empty(config(key: 'services.openai.api_key')))
        {
            throwException(
                exceptionClass: OpenAiApiKeyNotConfiguredException::class
            );
        }
    }

    /** @throws Throwable  */
    private function generateContentImages(): void
    {
        try
        {
            $imagesArray = (array) $this->blogPost->prompt->content_images;

            foreach ($imagesArray as $key => $prompt)
            {
                $this->processSingleImage(prompt: (string) $prompt, key: (string) $key);
            }

            if (notEmpty(value: $this->imageKeys))
            {
                $this->processBlogPostContent();
            }

            $this->deleteTempDirectory();
        }
        catch (Exception $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception|Throwable */
    private function processSingleImage(string $prompt, string $key): void
    {
        try
        {
            DB::transaction(callback: function () use ($prompt, $key): void
            {
                $tempPath = $this->getTempPath();

                $this->generateContentImage(prompt: $prompt, tempPath: $tempPath);

                $image = $this->blogPost->images()->create(attributes: [
                    'type'        => BlogImageTypeEnum::CONTENT,
                    'description' => $prompt,
                    'files'       => [],
                ]);

                $images = $this->generateSizes(
                    tempPath: $tempPath,
                    image   : $image
                );

                $image->updateQuietly(['files' => $images]);

                $this->imageKeys[$key] = "img={$image->id}";
            });
        }
        catch (Exception $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception */
    private function generateContentImage(string $prompt, string $tempPath): void
    {
        app()->make(abstract: GenerateActionInterface::class)->handle(
            tempPath    : $tempPath,
            promptString: $prompt,
        );
    }

    /** @throws Throwable */
    private function processBlogPostContent(): void
    {
        try
        {
            DB::transaction(callback: function (): void
            {
                $newContent = str_replace(
                    search : array_keys($this->imageKeys),
                    replace: array_values($this->imageKeys),
                    subject: $this->blogPost->content
                );

                $this->blogPost->updateQuietly(attributes: [
                    'content' => $newContent,
                ]);
            });
        }
        catch (Exception $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /**
     * @return array<int, object{width: int, image: string}>
     *
     * @throws BindingResolutionException
     */
    private function generateSizes(string $tempPath, BlogImage $image): array
    {
        $contentPath = $this->blogPost->contentImagePath(id: $image->id);
        $storageDisk = StorageDiskEnum::BLOG_IMAGES;
        $storePath   = mb_rtrim(string: $contentPath, characters: '/');

        if (! $storageDisk->disk()->exists(path: $storePath))
        {
            $storageDisk->disk()->makeDirectory(path: $storePath);
        }

        $imageSizes = app()->make(abstract: ResizeActionInterface::class)->handle(
            sourceFile : $tempPath,
            destination: $contentPath,
            storageDisk: $storageDisk,
        );

        $contents = $storageDisk->disk()->get($tempPath);

        if ($contents !== null)
        {
            $storageDisk->disk()->put(
                path    : $contentPath . basename(path: $tempPath),
                contents: $contents
            );
        }

        return $imageSizes;
    }

    private function getTempPath(): string
    {
        $format = $this->getImageFormat();

        return $this->blogPost->tempImagePath(
            file: 'original.' . $format,
            id  : Str::uuid()->toString()
        );
    }

    private function getImageFormat(): string
    {
        $defaultFormat = 'png';

        /** @var string|mixed $imageFormat */
        $imageFormat = config(
            key    : 'blog.image.format',
            default: $defaultFormat
        );

        return is_string(value: $imageFormat)
            ? $imageFormat
            : $defaultFormat;
    }

    /** @throws Throwable */
    private function schedulePost(): void
    {
        try
        {
            DB::transaction(callback: function (): void
            {
                $this->blogPost->updateQuietly(attributes: [
                    'status' => PostStatusEnum::SCHEDULED,
                ]);
            });
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    private function findBlogPost(): ?BlogPost
    {
        return BlogPost::query()
            ->with(relations: ['prompt', 'featured'])
            ->find(id: $this->postId);
    }

    private function deleteTempDirectory(): void
    {
        try
        {
            StorageDiskEnum::BLOG_IMAGES->disk()->deleteDirectory(
                directory: $this->blogPost->id . '/temp'
            );
        }
        catch (Exception $exception)
        {
            report(exception: $exception);
        }
    }
}
