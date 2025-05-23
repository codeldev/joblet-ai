<?php

declare(strict_types=1);

namespace App\Contracts\Services\Blog;

interface FeaturedImageGenerationServiceInterface
{
    public function handle(): void;
}
