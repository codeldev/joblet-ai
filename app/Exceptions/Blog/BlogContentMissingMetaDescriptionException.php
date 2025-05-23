<?php

declare(strict_types=1);

namespace App\Exceptions\Blog;

use Exception;

final class BlogContentMissingMetaDescriptionException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            message: trans(key: 'exception.blog.content.missing.meta.description')
        );
    }
}
