<?php

declare(strict_types=1);

namespace App\Exceptions\Backups;

use Exception;

final class MissingBackupConfigurationException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'exceptions.backups.config.missing'));
    }
}
