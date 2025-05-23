<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\AiProviders\Anthropic\AnthropicClientInterface;
use App\Contracts\Services\AiProviders\Anthropic\AnthropicInterface;
use App\Contracts\Services\AiProviders\OpenAI\ChatGptInterface;
use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use App\Services\AiProviders\Anthropic\Anthropic;
use App\Services\AiProviders\Anthropic\AnthropicClient;
use App\Services\AiProviders\OpenAI\ChatGpt;
use App\Services\AiProviders\OpenAI\OpenAIClient;
use Illuminate\Support\ServiceProvider;
use Override;

final class AiServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: ChatGptInterface::class,
            concrete: ChatGpt::class
        );

        $this->app->bind(
            abstract: AnthropicInterface::class,
            concrete: Anthropic::class
        );

        $this->app->bind(
            abstract: OpenAIClientInterface::class,
            concrete: OpenAIClient::class
        );

        $this->app->bind(
            abstract: AnthropicClientInterface::class,
            concrete: AnthropicClient::class
        );
    }
}
