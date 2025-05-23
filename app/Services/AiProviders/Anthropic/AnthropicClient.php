<?php

declare(strict_types=1);

namespace App\Services\AiProviders\Anthropic;

use App\Contracts\Services\AiProviders\Anthropic\AnthropicClientInterface;
use App\Exceptions\AiProviders\AnthropicApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostContentModelNotSetException;
use Claude\Claude3Api\Client;
use Claude\Claude3Api\Config;
use Claude\Claude3Api\Models\Content\TextContent;
use Claude\Claude3Api\Models\Message;
use Claude\Claude3Api\Models\Tool;
use Claude\Claude3Api\Requests\MessageRequest;
use Claude\Claude3Api\Responses\MessageResponse;
use Exception;

/**
 * @phpstan-type MessageParameters array{
 *     model: string,
 *     max_tokens?: int,
 *     temperature?: float,
 *     system_message?: string,
 *     messages: array<int, array{role: string, content: string}>,
 *     tools?: array<int, mixed>
 * }
 */
final class AnthropicClient implements AnthropicClientInterface
{
    private ?Client $client = null;

    /** @throws Exception  */
    public function client(string $apiKey): self
    {
        $maxTokens = $this->getMaxTokens();
        $feature   = $this->getBetaFeature();
        $config    = $this->getApiConfig(apiKey: $apiKey, maxTokens: $maxTokens);

        if ($maxTokens !== null)
        {
            $config->enable128kOutputWithTokens();
        }

        if ($feature !== null)
        {
            $config->enableBetaFeature(featureName: $feature);
        }

        $this->client = new Client(config: $config);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws Exception
     */
    public function sendMessage(array $parameters): MessageResponse
    {
        if (! $this->client instanceof Client)
        {
            throwException(
                exceptionClass: AnthropicApiKeyNotConfiguredException::class
            );
        }

        /** @var Client $client */
        $client = $this->client;

        return $client->sendMessage(
            request: $this->buildRequest(parameters: $parameters)
        );
    }

    /** @param array<string, mixed> $parameters */
    public function buildRequest(array $parameters): MessageRequest
    {
        $request = new MessageRequest();

        if (isset($parameters['model']) && is_string(value: $parameters['model']))
        {
            $request->setModel(model: $parameters['model']);
        }

        if (isset($parameters['max_tokens']))
        {
            /** @var int|float|string|bool $maxTokensValue */
            $maxTokensValue = $parameters['max_tokens'];

            $maxTokens = (int) $maxTokensValue;
            $request->setMaxTokens(maxTokens: $maxTokens);
        }

        if (isset($parameters['temperature']) && is_float(value: $parameters['temperature']))
        {
            $request->setTemperature(temperature: $parameters['temperature']);
        }

        if (isset($parameters['system_message']) && is_string(value: $parameters['system_message']))
        {
            $request->addSystemMessage(systemMessage: new Message(
                role   : 'system',
                content: [new TextContent(text: $parameters['system_message'])]
            ));
        }

        if (isset($parameters['messages']) && is_array(value: $parameters['messages']))
        {
            foreach ($parameters['messages'] as $message)
            {
                if (! is_array(value: $message))
                {
                    continue;
                }

                if (isset($message['role'], $message['content'])
                    && is_string(value: $message['role'])
                    && is_string(value: $message['content'])
                ) {
                    $request->addMessage(message: new Message(
                        role    : $message['role'],
                        content: [new TextContent(text: $message['content'])]
                    ));
                }
            }
        }

        if (
            isset($parameters['tools'])
            && is_array(value: $parameters['tools'])
        ) {
            foreach ($parameters['tools'] as $tool)
            {
                if ($tool instanceof Tool)
                {
                    $request->addTool(tool: $tool);
                }
            }
        }

        return $request;
    }

    /** @throws Exception  */
    private function getApiConfig(string $apiKey, ?string $maxTokens): Config
    {
        /** @var string|null $model */
        $model = config(key: 'blog.post.model');

        if ($model === null)
        {
            throwException(
                exceptionClass: BlogPostContentModelNotSetException::class
            );
        }

        /** @var string $modelString */
        $modelString = $model;

        return new Config(
            apiKey   : $apiKey,
            model    : $modelString,
            maxTokens: $maxTokens ?? '100000',
        );
    }

    private function getBetaFeature(): ?string
    {
        /** @var null|string $feature */
        $feature = config(key: 'services.anthropic.beta_feature');

        return is_string(value: $feature)
            ? $feature
            : null;
    }

    private function getMaxTokens(): ?string
    {
        /** @var null|string $maxTokens */
        $maxTokens = config(key: 'services.anthropic.max_tokens');

        return is_string(value: $maxTokens)
            ? $maxTokens
            : null;
    }
}
