<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

enum StorageDiskEnum: string
{
    case BLOG_IDEAS         = 'blog:ideas';
    case BLOG_PROMPTS       = 'blog:prompts';
    case BLOG_IMAGES        = 'blog:images';
    case BLOG_ERRORS        = 'blog:errors';
    case BLOG_UNPROCESSABLE = 'blog:unprocessable';

    public function disk(): Filesystem
    {
        return Storage::disk(name: $this->value);
    }
}
