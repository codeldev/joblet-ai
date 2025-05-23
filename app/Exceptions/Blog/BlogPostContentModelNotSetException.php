<?php

declare(strict_types=1);

namespace App\Exceptions\Blog;

use Exception;

final class BlogPostContentModelNotSetException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            message: trans(key: 'exception.blog.model.content.not.set')
        );
    }
}
