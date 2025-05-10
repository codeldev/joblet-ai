<?php

declare(strict_types=1);

return [
    'api_key'         => env(key: 'OPENAI_API_KEY'),
    'organization'    => env(key: 'OPENAI_ORGANIZATION'),
    'request_timeout' => env(key: 'OPENAI_REQUEST_TIMEOUT', default: 30),
    'model'           => env(key: 'OPEN_AI_MODEL', default: 'gpt-3.5-turbo'),
];
