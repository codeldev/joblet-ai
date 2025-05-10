<?php

declare(strict_types=1);

namespace App\Contracts\Services\Sitemap;

interface GeneratorServiceInterface
{
    public function get(): string;
}
