<?php

declare(strict_types=1);

namespace App\Services\AiProviders\OpenAI;

use App\Abstracts\Services\AiProviders\AiProviderAbstract;
use App\Contracts\Services\AiProviders\AiProviderInterface;
use App\Contracts\Services\AiProviders\OpenAI\ChatGptInterface;
use App\Exceptions\AiProviders\NoValidContentInResponseException;
use App\Facades\OpenAI;
use App\Models\BlogIdea;
use JsonException;
use OpenAI\Resources\Chat;
use OpenAI\Responses\Chat\CreateResponse;
use Throwable;

final class ChatGpt extends AiProviderAbstract implements AiProviderInterface, ChatGptInterface
{
    /**
     * @return array<string, string>
     *
     * @throws Throwable
     * @throws JsonException
     * @throws NoValidContentInResponseException
     */
    public function handle(BlogIdea $blogIdea): array
    {
        $this->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $blogIdea
        );

        return $this->get();
    }

    /**
     * @return array<string, string>
     *
     * @throws Throwable
     * @throws JsonException
     */
    public function get(): array
    {
        $apiKey = (string) $this->apiKey;
        $client = OpenAI::client(apiKey: $apiKey);

        /** @var Chat $chat */
        $chat = $client->chat();

        /** @var CreateResponse $response */
        $response = $chat->create(
            parameters: $this->buildPayload()
        );

        if (! notEmpty(value: $response->choices) || ! isset($response->choices[0]))
        {
            throwException(
                exceptionClass: NoValidContentInResponseException::class
            );
        }

        /** @var string $content */
        $content = $response->choices[0]->message->content;

        if ($content === '')
        {
            throwException(
                exceptionClass: NoValidContentInResponseException::class
            );
        }

        /** @var array<string, string> $result */
        $result = json_decode(
            json       : $content,
            associative: true,
            depth      : 500,
            flags      : JSON_THROW_ON_ERROR
        );

        return $result;
    }

    /** @return array<string, mixed> */
    private function buildPayload(): array
    {
        return [
            'model'    => $this->model,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => $this->systemPrompt,
                ],
                [
                    'role'    => 'user',
                    'content' => $this->userPrompt,
                ],
            ],
            'response_format' => [
                'type' => 'json_object',
            ],
        ];
    }
}
