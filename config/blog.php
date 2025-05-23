<?php

declare(strict_types=1);

return [
    'job' => [
        'attempts' => env(key: 'BLOG_JOB_ATTEMPTS', default: '60|300|600'),
    ],
    'ideas' => [
        'delay' => (int) env(key: 'BLOG_IDEAS_DELAY', default: 10),
    ],
    'post' => [
        'model'    => env(key: 'BLOG_POST_MODEL'),
        'schedule' => (int) env(key: 'BLOG_POST_SCHEDULE_DAYS', default: 2),
    ],
    'image' => [
        'delay'      => (int) env(key: 'BLOG_IMAGE_DELAY', default: 2),
        'size'       => env(key: 'BLOG_IMAGE_SIZE', default: '1024x1024'),
        'format'     => env(key: 'BLOG_IMAGE_FORMAT', default: 'png'),
        'model'      => env(key: 'BLOG_IMAGE_AI_MODEL', default: 'gpt-image-1'),
        'quality'    => env(key: 'BLOG_IMAGE_QUALITY', default: 'low'),
        'conversion' => [
            'format' => env(key: 'BLOG_IMAGE_CONVERSION_FORMAT', default: 'webp'),
            'sizes'  => [400, 700, 1000, 1300, 1600, 1920],
        ],
    ],
    'prompts' => [
        'system' => env(key: 'BLOG_PROMPT_SYSTEM', default: 'system.md'),
        'user'   => env(key: 'BLOG_PROMPT_USER', default: 'user.md'),
        'image'  => env(key: 'BLOG_PROMPT_IMAGE', default: 'image.md'),
    ],
];
