<?php

declare(strict_types=1);

namespace App\Services\Blog;

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
use App\Models\BlogPost;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class FeaturedImageGenerationService implements FeaturedImageGenerationServiceInterface
{
    private bool $imageStored = false;

    private readonly BlogPost $blogPost;

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
        $this->blogPost = $blogPost;
    }

    /** @throws Exception|Throwable */
    public function handle(): void
    {
        $this->runRequirementChecks();
        $this->generatePostImage();

        if ($this->imageStored)
        {
            $this->setHasFeaturedImage();
            $this->generateImageSizes();
            $this->queueContentImagesJob();
        }
    }

    private function queueContentImagesJob(): void
    {
        ProcessBlogContentImagesJob::dispatch(
            postId: $this->blogPost->id
        );
    }

    /** @throws Throwable */
    private function generateImageSizes(): void
    {
        $fileName    = 'original.' . $this->getImageFormat();
        $sourceFile  = $this->blogPost->featuredImagePath(file: $fileName);
        $destination = str_replace(search: $fileName, replace: '', subject: $sourceFile);

        $images = app()->make(abstract: ResizeActionInterface::class)->handle(
            sourceFile : $sourceFile,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES,
        );

        $this->storeImageSizes(images: $images);
    }

    /**
     * @param  array<int, object{width: int, image: string}>  $images
     *
     * @throws Throwable
     */
    private function storeImageSizes(array $images): void
    {
        try
        {
            DB::transaction(callback: function () use ($images): void
            {
                $this->blogPost->images()->create(attributes: [
                    'type'        => BlogImageTypeEnum::FEATURED,
                    'description' => $this->blogPost->prompt->image_prompt,
                    'files'       => array_column($images, 'image', 'width'),
                ]);
            });
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception */
    private function runRequirementChecks(): void
    {
        $this->validatePostStatus();
        $this->validateNoExistingImage();
        $this->validateImagePrompt();
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
    private function validateNoExistingImage(): void
    {
        if ($this->blogPost->featured()->exists())
        {
            throwException(
                exceptionClass: BlogPostImageAlreadyGeneratedException::class
            );
        }
    }

    /** @throws Exception */
    private function validateImagePrompt(): void
    {
        if (empty($this->blogPost->prompt->image_prompt))
        {
            throwException(
                exceptionClass: BlogPromptImagePromptMissingException::class
            );
        }
    }

    /** @throws Exception */
    private function generatePostImage(): void
    {
        try
        {
            app()->make(abstract: GenerateActionInterface::class)->handle(
                tempPath    : $this->getImagePath(),
                promptString: $this->blogPost->prompt->image_prompt,
            );

            $this->imageStored = true;
        }
        catch (Exception $exception)
        {
            $this->imageStored = false;

            report(exception: $exception);

            throw $exception;
        }
    }

    private function getImagePath(): string
    {
        $format = $this->getImageFormat();

        return $this->blogPost->featuredImagePath(file: 'original.' . $format);
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
    private function setHasFeaturedImage(): void
    {
        try
        {
            DB::transaction(callback: function (): void
            {
                $this->blogPost->updateQuietly(attributes: [
                    'has_featured_image' => true,
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
}
