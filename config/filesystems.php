<?php

declare(strict_types=1);

return [
    'default' => env(key: 'FILESYSTEM_DISK', default: 'local'),
    'disks'   => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private'),
            'serve'  => true,
            'throw'  => false,
            'report' => false,
        ],
        'public' => [
            'driver'     => 'local',
            'root'       => storage_path(path: 'app/public'),
            'url'        => env(key: 'APP_URL') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],
        'google' => [
            'driver'       => 'google',
            'clientId'     => env(key: 'GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env(key: 'GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env(key: 'GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folder'       => env(key: 'GOOGLE_DRIVE_FOLDER'),
        ],
        'blog:prompts' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private/blog/prompts'),
            'serve'  => false,
            'throw'  => false,
            'report' => false,
        ],
        'blog:errors' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private/blog/errors'),
            'serve'  => false,
            'throw'  => false,
            'report' => false,
        ],
        'blog:ideas' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private/blog/ideas'),
            'serve'  => false,
            'throw'  => false,
            'report' => false,
        ],
        'blog:unprocessable' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private/blog/unprocessable'),
            'serve'  => false,
            'throw'  => false,
            'report' => false,
        ],
        'blog:images' => [
            'driver'     => 'local',
            'root'       => storage_path(path: 'app/public/blog'),
            'url'        => env(key: 'APP_URL') . '/storage/blog',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],
    ],
    'links' => [
        public_path(path: 'storage') => storage_path(path: 'app/public'),
    ],
];
