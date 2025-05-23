<?php

declare(strict_types=1);

namespace App\Contracts\Services\Blog;

interface PostGenerationServiceInterface
{
    public function handle(): void;
}
