<?php

declare(strict_types=1);

return [
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'expire'   => 60,
            'throttle' => (int) env(key: 'AUTH_LOCKOUT_SECONDS', default: 60),
            'attempts' => (int) env(key: 'AUTH_LOGIN_ATTEMPTS', default: 5),
        ],
    ],
];
