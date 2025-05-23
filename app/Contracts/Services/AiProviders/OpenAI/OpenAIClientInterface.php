<?php

declare(strict_types=1);

namespace App\Contracts\Services\AiProviders\OpenAI;

interface OpenAIClientInterface
{
    public function client(string $apiKey): object;

    public function chat(): object;

    /** @param array<string, mixed> $parameters */
    public function create(array $parameters): object;

    public function images(): object;
}
