<?php

declare(strict_types=1);

namespace App\Contracts\Console\Commands\Blog;

interface QueueBlogIdeasCommandInterface
{
    public function handle(): int;
}
