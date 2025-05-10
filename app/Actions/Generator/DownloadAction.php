<?php

declare(strict_types=1);

namespace App\Actions\Generator;

use App\Models\Generated;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class DownloadAction
{
    public function handle(Generated $generated): BinaryFileResponse
    {
        $fileData = $this->buildFileData(
            generated: $generated
        );

        return response()->download(
            file   : $fileData['path'],
            name   : $fileData['name'],
            headers: $fileData['headers']
        )->deleteFileAfterSend();
    }

    /** @return array{name: string, path: string, headers: array<string, string>} */
    private function buildFileData(Generated $generated): array
    {
        $disk = Storage::disk(name: 'local');
        $file = $this->buildFileName(generated: $generated) . '.txt';

        $disk->put(
            path     : $file,
            contents : $generated->generated_content_raw
        );

        return [
            'name'    => $file,
            'path'    => $disk->path(path: $file),
            'headers' => ['Content-Type' => 'text/plain'],
        ];
    }

    private function buildFileName(Generated $generated): string
    {
        return str(string: "{$generated->user->name}-{$generated->id}")
            ->slug()
            ->toString();
    }
}
