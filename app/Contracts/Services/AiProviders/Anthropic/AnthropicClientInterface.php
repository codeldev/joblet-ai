<?php

declare(strict_types=1);

namespace App\Contracts\Services\AiProviders\Anthropic;

use Claude\Claude3Api\Responses\MessageResponse;
use Exception;

interface AnthropicClientInterface
{
    /** @throws Exception  */
    public function client(string $apiKey): self;

    /**  @param array<string, mixed> $parameters */
    public function sendMessage(array $parameters): MessageResponse;
}
