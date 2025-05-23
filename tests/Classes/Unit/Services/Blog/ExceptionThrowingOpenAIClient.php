<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Blog;

use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use Exception;

final readonly class ExceptionThrowingOpenAIClient implements OpenAIClientInterface
{
    public function __construct(private Exception $exception) {}

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
        return (object) [];
    }

    public function images(): object
    {
        return new ExceptionThrowingImagesClient(exception: $this->exception);
    }
}
