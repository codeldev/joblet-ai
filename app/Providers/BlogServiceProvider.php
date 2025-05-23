<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\Blog\ContentImagesGenerationServiceInterface;
use App\Contracts\Services\Blog\FeaturedImageGenerationServiceInterface;
use App\Contracts\Services\Blog\PostGenerationServiceInterface;
use App\Services\Blog\ContentImagesGenerationService;
use App\Services\Blog\FeaturedImageGenerationService;
use App\Services\Blog\PostGenerationService;
use Illuminate\Support\ServiceProvider;
use Override;

final class BlogServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: FeaturedImageGenerationServiceInterface::class,
            concrete: FeaturedImageGenerationService::class
        );

        $this->app->bind(
            abstract: PostGenerationServiceInterface::class,
            concrete: PostGenerationService::class
        );

        $this->app->bind(
            abstract: ContentImagesGenerationServiceInterface::class,
            concrete: ContentImagesGenerationService::class
        );
    }
}
