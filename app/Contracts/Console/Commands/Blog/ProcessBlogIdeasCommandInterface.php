<?php

declare(strict_types=1);

namespace App\Contracts\Console\Commands\Blog;

interface ProcessBlogIdeasCommandInterface
{
    public function handle(): int;
}
