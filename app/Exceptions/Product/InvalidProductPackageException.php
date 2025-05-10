<?php

declare(strict_types=1);

namespace App\Exceptions\Product;

use Exception;

final class InvalidProductPackageException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'exception.package.invalid'));
    }
}
