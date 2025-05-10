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
    ],
    'links' => [
        public_path(path: 'storage') => storage_path(path: 'app/public'),
    ],
];
