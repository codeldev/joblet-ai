<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Images;

use App\Enums\StorageDiskEnum;

interface ResizeActionInterface
{
    /** @return array<int, object{width: int, image: string}> */
    public function handle(
        string $sourceFile,
        string $destination,
        StorageDiskEnum $storageDisk
    ): array;
}
