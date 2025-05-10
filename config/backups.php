<?php

declare(strict_types=1);

return [
    'zip_file'    => env(key: 'BACKUP_ZIP_FILE', default: 'app-:date.zip'),
    'date_format' => env(key: 'BACKUP_DATE_FORMAT', default: 'Y-m-d-H-i-s'),
    'upload_to'   => env(key: 'BACKUP_STORAGE_FILE', default: 'backups/app/:file'),
    'list_from'   => env(key: 'BACKUP_STORAGE_PATH', default: 'backups/app'),
];
