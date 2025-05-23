<?php

declare(strict_types=1);

namespace App\Contracts\Services\AiProviders\Anthropic;

use App\Models\BlogIdea;

interface AnthropicInterface
{
    /** @return array<string, string> */
    public function handle(BlogIdea $blogIdea): array;
}
