<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Blog;

use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use stdClass;

final class FakeOpenAIClient implements OpenAIClientInterface
{
    public function client(string $apiKey): object
    {
        return $this;
    }

    public function chat(): object
    {
        return $this;
    }

    public function create(array $parameters): object
    {
        return new stdClass();
    }

    public function images(): object
    {
        return new FakeOpenAIImagesClient();
    }
}
