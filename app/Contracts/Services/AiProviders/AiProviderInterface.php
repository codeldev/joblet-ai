<?php

declare(strict_types=1);

namespace App\Contracts\Services\AiProviders;

use App\Models\BlogIdea;

interface AiProviderInterface
{
    /**  @return array<string, string> */
    public function handle(BlogIdea $blogIdea): array;

    /** @return array<string, string> */
    public function get(): array;
}
