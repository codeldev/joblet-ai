<?php

declare(strict_types=1);

namespace App\Services\AiProviders\OpenAI;

use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use Exception;
use OpenAI;
use OpenAI\Client;
use OpenAI\Resources\Chat;
use OpenAI\Responses\Chat\CreateResponse;

final class OpenAIClient implements OpenAIClientInterface
{
    private ?Client $client = null;

    public function client(string $apiKey): self
    {
        $this->client = OpenAI::client(apiKey: $apiKey);

        return $this;
    }

    /** @throws Exception  */
    public function chat(): Chat
    {
        if (! $this->client instanceof Client)
        {
            throwException(
                exceptionClass: OpenAiApiKeyNotConfiguredException::class
            );
        }

        /** @var Client $client */
        $client = $this->client;

        return $client->chat();
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws Exception
     */
    public function create(array $parameters): object
    {
        if (! $this->client instanceof Client)
        {
            throwException(
                exceptionClass: OpenAiApiKeyNotConfiguredException::class
            );
        }

        // @codeCoverageIgnoreStart
        /** @var Client $client */
        $client = $this->client;

        /** @var CreateResponse $response */
        $response = $client->chat()->create(parameters: $parameters);

        return $response;
        // @codeCoverageIgnoreEnd
    }

    /** @throws Exception */
    public function images(): object
    {
        if (! $this->client instanceof Client)
        {
            throwException(
                exceptionClass: OpenAiApiKeyNotConfiguredException::class
            );
        }

        // @codeCoverageIgnoreStart
        /** @var Client $client */
        $client = $this->client;

        return $client->images();
        // @codeCoverageIgnoreEnd
    }
}
