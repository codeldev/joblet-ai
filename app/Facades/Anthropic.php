<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\Services\AiProviders\Anthropic\AnthropicClientInterface;
use Claude\Claude3Api\Responses\MessageResponse;
use Exception;
use Illuminate\Support\Facades\Facade;

/**
 * @method static AnthropicClientInterface client(string $apiKey)
 * @method static MessageResponse sendMessage(array $parameters)
 *
 * @see AnthropicClientInterface
 */
final class Anthropic extends Facade
{
    /**
     * @param  array<int, mixed>  $responses
     */
    public static function fake(array $responses = []): void
    {
        self::swap(instance: new class($responses) implements AnthropicClientInterface
        {
            private int $currentResponse = 0;

            /**
             * @param  array<int, mixed>  $responses
             */
            public function __construct(private readonly array $responses) {}

            public function client(string $apiKey): AnthropicClientInterface
            {
                return $this;
            }

            /** @param  array<string, mixed>  $parameters */
            public function sendMessage(array $parameters): MessageResponse
            {
                if (! notEmpty(value: $this->responses))
                {
                    return $this->fakeAnthropicResponse(content: '{"meta_title":"Test Title","meta_description":"Test Description","post_summary":"Test Summary","post_content":"Test Content","image_prompt":"Test Image Prompt"}');
                }

                /** @var MessageResponse|Exception $response */
                $response = $this->responses[$this->currentResponse] ?? $this->responses[0];

                $this->currentResponse = ($this->currentResponse + 1) % count(value: $this->responses);

                if ($response instanceof Exception)
                {
                    throw $response;
                }

                /** @var MessageResponse $response */
                return $response;
            }

            /**
             * Create a fake Anthropic response
             */
            private function fakeAnthropicResponse(?string $content = null): MessageResponse
            {
                $defaultContent = '{"meta_title":"Test Title","meta_description":"Test Description","post_summary":"Test Summary","post_content":"Test Content","image_prompt":"Test Image Prompt"}';

                $toolUseContent = [
                    'type'  => 'tool_use',
                    'name'  => 'generate_blog_json',
                    'input' => json_decode(
                        json       : $content ?? $defaultContent,
                        associative: true,
                        depth      : 500,
                        flags      : JSON_THROW_ON_ERROR
                    ),
                ];

                return new MessageResponse(data: [
                    'id'            => 'msg_' . uniqid(prefix: 'test', more_entropy: true),
                    'model'         => 'claude-3-opus-20240229',
                    'type'          => 'message',
                    'role'          => 'assistant',
                    'content'       => [$toolUseContent],
                    'stop_reason'   => 'end_turn',
                    'stop_sequence' => null,
                    'usage'         => [
                        'input_tokens'  => fake()->numberBetween(int1: 100, int2: 700),
                        'output_tokens' => fake()->numberBetween(int1: 100, int2: 700),
                    ],
                ]);
            }
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return AnthropicClientInterface::class;
    }
}
