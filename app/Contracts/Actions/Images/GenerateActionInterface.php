<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Images;

use Exception;

interface GenerateActionInterface
{
    /** @throws Exception */
    public function handle(string $tempPath, string $promptString): void;
}
