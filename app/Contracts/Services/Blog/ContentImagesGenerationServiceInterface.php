<?php

declare(strict_types=1);

namespace App\Contracts\Services\Blog;

interface ContentImagesGenerationServiceInterface
{
    public function handle(): void;
}
