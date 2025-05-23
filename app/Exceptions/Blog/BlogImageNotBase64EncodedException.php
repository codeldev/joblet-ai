<?php

declare(strict_types=1);

namespace App\Exceptions\Blog;

use Exception;

final class BlogImageNotBase64EncodedException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            message: trans(key: 'exception.blog.image.base64')
        );
    }
}
