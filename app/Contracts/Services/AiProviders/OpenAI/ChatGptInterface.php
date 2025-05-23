<?php

declare(strict_types=1);

namespace App\Contracts\Services\AiProviders\OpenAI;

use App\Models\BlogIdea;

interface ChatGptInterface
{
    /** @return array<string, string> */
    public function handle(BlogIdea $blogIdea): array;
}
