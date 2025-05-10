<?php

declare(strict_types=1);

namespace App\Exceptions\Uploads;

use Exception;

final class UploadedResumeEmptyException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'exception.upload.resume.empty'));
    }
}
