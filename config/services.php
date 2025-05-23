<?php

declare(strict_types=1);

return [
    'resend' => [
        'key' => env(key: 'RESEND_KEY'),
    ],
    'openai' => [
        'api_key' => env(key: 'OPENAI_API_KEY'),
    ],
    'anthropic' => [
        'api_key'      => env(key: 'ANTHROPIC_API_KEY'),
        'beta_feature' => env(key: 'ANTHROPIC_BETA_FEATURE'),
        'max_tokens'   => env(key: 'ANTHROPIC_MAX_TOKENS'),
    ],
];
