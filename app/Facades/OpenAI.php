<?php

/** @noinspection PhpSameParameterValueInspection */

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use Exception;
use Illuminate\Support\Facades\Facade;
use OpenAI\Responses\Chat\CreateResponse;

/**
 * @method static OpenAIClientInterface client(string $apiKey)
 *
 * @see OpenAIClientInterface
 */
final class OpenAI extends Facade
{
    /** @param array<int, mixed> $responses */
    public static function fake(array $responses = []): void
    {
        self::swap(instance: new class($responses) implements OpenAIClientInterface
        {
            private int $currentResponse = 0;

            /** @param array<int, mixed> $responses */
            public function __construct(private readonly array $responses) {}

            public function client(string $apiKey): object
            {
                return $this;
            }

            public function chat(): object
            {
                return $this;
            }

            public function images(): object
            {
                return $this;
            }

            /**
             * @param  array<string, mixed>  $parameters
             */
            public function create(array $parameters): object
            {
                if (! notEmpty(value: $this->responses))
                {
                    return $this->fakeOpenAiResponse(content: '{"title":"Test Title","content":"Test Content"}');
                }

                /** @var object|Exception $response */
                $response = $this->responses[$this->currentResponse] ?? $this->responses[0];

                $this->currentResponse = ($this->currentResponse + 1) % count(value: $this->responses);

                if ($response instanceof Exception)
                {
                    throw $response;
                }

                /** @var object $response */
                return $response;
            }

            private function fakeOpenAiResponse(?string $content): CreateResponse
            {
                return CreateResponse::fake(override: [
                    'id'      => 'chatcmpl-' . uniqid(prefix: 'text', more_entropy: true),
                    'object'  => 'chat.completion',
                    'created' => time(),
                    'model'   => 'gpt-3.5-turbo',
                    'choices' => [
                        [
                            'index'         => 0,
                            'message'       => [
                                'role'    => 'assistant',
                                'content' => $content,
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                    'usage'   => [
                        'prompt_tokens'     => $prompt     = fake()->numberBetween(int1: 100, int2: 700),
                        'completion_tokens' => $completion = fake()->numberBetween(int1: 100, int2: 700),
                        'total_tokens'      => ($prompt + $completion),
                    ],
                ]);
            }
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return OpenAIClientInterface::class;
    }
}
