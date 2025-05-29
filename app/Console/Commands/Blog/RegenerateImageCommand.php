<?php

declare(strict_types=1);

namespace App\Console\Commands\Blog;

use App\Concerns\HasCommandsTrait;
use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Enums\BlogImageTypeEnum;
use App\Enums\StorageDiskEnum;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    name: 'blog:image:regenerate',
    description: 'Regenerate an image for a blog post using OpenAI and save resized images.'
)]
final class RegenerateImageCommand extends Command
{
    use HasCommandsTrait;

    public function handle(): int
    {
        $posts = BlogPost::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        if ($posts->isEmpty())
        {
            $this->outputErrorMessage(
                message: 'No blog posts found.'
            );

            return self::FAILURE;
        }

        $postOptions = $posts->mapWithKeys(
            fn ($post) => [$post->id => 'ID: ' . $post->id . ' | ' . $post->title]
        )->toArray();

        $postId = select(
            label: 'Which blog post would you like to regenerate images for?',
            options: $postOptions,
            scroll: 10
        );

        if (! $postId)
        {
            $this->outputErrorMessage(
                message: 'No blog post selected.'
            );

            return self::FAILURE;
        }

        $images = BlogImage::query()
            ->where('post_id', $postId)
            ->orderBy('id')
            ->get();

        if ($images->isEmpty())
        {
            $this->outputErrorMessage(
                message: 'No images found for the selected blog post.'
            );

            return self::FAILURE;
        }

        $imageOptions = $images->mapWithKeys(
            fn ($img) => [$img->id => 'ID: ' . $img->id . ' | Type: ' . $img->type->name]
        )->toArray();

        $imageId = select(
            label: 'Which image would you like to regenerate?',
            options: $imageOptions,
            scroll: 10
        );

        if (! $imageId)
        {
            $this->outputErrorMessage(
                message: 'No image selected.'
            );

            return self::FAILURE;
        }

        return $this->startRegeneration(postId: $postId, imageId: $imageId);
    }

    private function startRegeneration(string $postId, string $imageId): int
    {
        try
        {
            $blogPost = BlogPost::query()
                ->with(relations: ['prompt', 'images', 'featured'])
                ->findOrFail(id: $postId);

            $image  = BlogImage::findOrFail(id: $imageId);
            $prompt = $this->getImagePrompt(blogPost: $blogPost, image: $image);

            if (empty($prompt))
            {
                $this->outputErrorMessage(
                    message: 'No prompt found for the selected image.'
                );

                return self::FAILURE;
            }

            spin(
                callback: fn () => $this->regenerateImage(
                    blogPost: $blogPost,
                    image   : $image,
                    prompt  : $prompt
                ),
                message : 'Regenerating image...'
            );

            $this->outputInfoMessage(
                message: 'Image regenerated successfully.'
            );

            return self::SUCCESS;
        }
        catch (Exception | Throwable $exception)
        {
            $this->outputErrorMessage(
                message: $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    private function getImagePrompt(BlogPost $blogPost, BlogImage $image): string
    {
        return match ($image->type)
        {
            BlogImageTypeEnum::FEATURED => $blogPost->prompt->image_prompt ?? '',
            BlogImageTypeEnum::CONTENT  => $image->description             ?? '',
            default                     => '',
        };
    }

    /** @throws Exception|Throwable */
    private function regenerateImage(BlogPost $blogPost, BlogImage $image, string $prompt): void
    {
        $imagePath   = $this->getImagePath(blogPost: $blogPost, image: $image);
        $storageDisk = StorageDiskEnum::BLOG_IMAGES;
        $storePath   = dirname($imagePath);

        if (! $storageDisk->disk()->exists(path: $storePath))
        {
            $storageDisk->disk()->makeDirectory(path: $storePath);
        }

        $this->generateImage(
            imagePath: $imagePath,
            prompt   : $prompt
        );

        $this->generateImageSizes(
            blogPost : $blogPost,
            image    : $image,
            imagePath: $imagePath
        );
    }

    private function getImagePath(BlogPost $blogPost, BlogImage $image): string
    {
        $format = $this->getImageFormat();

        return match ($image->type)
        {
            BlogImageTypeEnum::FEATURED => $blogPost->featuredImagePath(file: 'original.' . $format),
            BlogImageTypeEnum::CONTENT  => $blogPost->contentImagePath(id: $image->id) . 'original.' . $format,
            default                     => '',
        };
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

    /** @throws Exception */
    private function generateImage(string $imagePath, string $prompt): void
    {
        app()->make(abstract: GenerateActionInterface::class)->handle(
            tempPath    : $imagePath,
            promptString: $prompt,
        );
    }

    /** @throws Throwable */
    private function generateImageSizes(BlogPost $blogPost, BlogImage $image, string $imagePath): void
    {
        $destination = match ($image->type)
        {
            BlogImageTypeEnum::FEATURED => str_replace(
                search: 'original.' . $this->getImageFormat(),
                replace: '',
                subject: $imagePath
            ),
            BlogImageTypeEnum::CONTENT => $blogPost->contentImagePath(id: $image->id),
            default                    => '',
        };

        $imageSizes = app()->make(abstract: ResizeActionInterface::class)->handle(
            sourceFile : $imagePath,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES,
        );

        DB::transaction(callback: static function () use ($image, $imageSizes): void
        {
            $image->updateQuietly(['files' => $imageSizes]);
        });
    }
}
