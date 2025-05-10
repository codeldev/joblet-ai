<?php

declare(strict_types=1);

namespace App\Actions\Generator;

use App\Exceptions\Uploads\UploadedResumeEmptyException;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\PdfToText\Pdf;

final class UploadAction
{
    private string $fileName = '';

    private string $originalName = '';

    private Filesystem $disk;

    private TemporaryUploadedFile $file;

    public function handle(TemporaryUploadedFile $file, callable $success, callable $failed): void
    {
        $this->file = $file;

        $this->setDisk();
        $this->generateFileName();
        $this->setOriginalName();

        try
        {
            $this->storeFile();

            $success($this->originalName);
        }
        catch (Exception $e)
        {
            report(exception: $e);

            $this->deleteFile();

            $failed($e->getMessage());
        }
    }

    private function deleteFile(): void
    {
        if ($this->disk->exists(path: $this->fileName))
        {
            $this->disk->delete(paths: $this->fileName);
        }
    }

    /** @throws UploadedResumeEmptyException */
    private function storeFile(): void
    {
        $content = $this->getResumeContent();

        if (! notEmpty(value: $content))
        {
            throw new UploadedResumeEmptyException;
        }

        $this->updateUserContent(content: $content);
        $this->deleteFile();
    }

    private function getResumeContent(): string
    {
        $this->disk->putFileAs(path: '', file: $this->file, name: $this->fileName);

        return Pdf::getText(pdf: $this->disk->path(path: $this->fileName));
    }

    private function updateUserContent(string $content): void
    {
        /** @var User $user */
        $user = auth()->user();

        $user->updateQuietly(attributes: [
            'cv_filename' => $this->originalName,
            'cv_content'  => $content,
        ]);
    }

    private function setOriginalName(): void
    {
        $this->originalName = $this->file->getClientOriginalName();
    }

    private function setDisk(): void
    {
        $this->disk = Storage::disk(name: 'local');
    }

    private function generateFileName(): void
    {
        $fileName  = Str::random(40);
        $fileName .= '.';
        $fileName .= $this->file->extension();

        $this->fileName = $fileName;
    }
}
