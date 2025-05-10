<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Generator;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

interface UploadActionInterface
{
    public function handle(TemporaryUploadedFile $file, callable $success, callable $failed): void;
}
