<?php

declare(strict_types=1);

namespace App\Exceptions\AiProviders;

use Exception;

final class OpenAiApiKeyNotConfiguredException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            message: trans(key: 'exception.ai.api.openai')
        );
    }
}
