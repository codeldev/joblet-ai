<?php

declare(strict_types=1);

namespace App\Livewire\Generator;

use App\Actions\Generator\UploadAction;
use App\Concerns\HasNotificationsTrait;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

final class Upload extends Component
{
    use HasNotificationsTrait;

    use WithFileUploads;

    public ?string $uploadKey = null;

    public ?string $fileName  = null;

    public bool $processing   = false;

    #[Validate(['required', 'file', 'mimes:pdf', 'max:5120'])]
    public ?TemporaryUploadedFile $file = null;

    public function mount(): void
    {
        $this->generateUploadKey();

        if (Auth::check())
        {
            /** @var User $user */
            $user = auth()->user();

            $this->fileName = $user->cv_filename;
        }
    }

    public function render(): View
    {
        return view(view: 'livewire.generator.upload');
    }

    public function updatedFile(): void
    {
        $this->validate();

        $this->processing = true;

        $this->dispatch('process-resume-upload');
    }

    #[On('process-resume-upload')]
    public function processUpload(UploadAction $action): void
    {
        if (! $this->file instanceof TemporaryUploadedFile)
        {
            $this->notifyError(
                message: trans(key: 'generator.form.resume.upload.no.file')
            );

            $this->processing = false;

            return;
        }

        $action->handle(
            file    : $this->file,
            success : fn (string $fileName) => $this->successfulUpload(
                fileName: $fileName
            ),
            failed  : fn (string $error) => $this->notifyError(
                message: $error
            )
        );

        $this->processing = false;
    }

    private function successfulUpload(string $fileName): void
    {
        $this->notifySuccess(
            message: trans(key: 'generator.form.resume.upload.success')
        );

        $this->fileName = $fileName;

        $this->generateUploadKey();
        $this->clearValidation();
        $this->reset('file');
    }

    private function generateUploadKey(): void
    {
        $this->uploadKey = Str::random(40);
    }
}
