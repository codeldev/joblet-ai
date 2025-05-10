<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Actions\Generator\DownloadAction;
use App\Actions\Generator\SaveAction;
use App\Concerns\HasNotificationsTrait;
use App\Models\Generated;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class Letter extends Component
{
    use HasNotificationsTrait;

    public ?Generated $generated = null;

    public ?string $generatedContentText = null;

    public ?string $generatedContentHtml = null;

    public function render(): View
    {
        return view(view: 'livewire.dashboard.letter');
    }

    #[On('view-generated-letter')]
    public function viewGeneratedLetter(string | int $generatedId): void
    {
        $this->generated = Generated::find(id: $generatedId);

        if (! $this->generated instanceof Generated)
        {
            $this->notifyError(
                message: trans(key: 'letter.not.found')
            );

            return;
        }

        if ($this->canPerformAction(ability: 'view'))
        {
            $this->generatedContentHtml = $this->generated->generated_content_html;
            $this->generatedContentText = $this->generated->generated_content_raw;

            /** @var object $modal */
            $modal = Flux::modal(name: 'show-viewable');

            if (method_exists(object_or_class: $modal, method: 'show'))
            {
                $modal->show();
            }
        }
    }

    #[On('dashboard-view-letter')]
    public function view(Generated $generated): void
    {
        $this->generated = $generated;

        if ($this->canPerformAction(ability: 'view'))
        {
            $this->generatedContentHtml = $generated->generated_content_html;
            $this->generatedContentText = $generated->generated_content_raw;

            /** @var object $modal */
            $modal = Flux::modal(name: 'show-viewable');

            if (method_exists(object_or_class: $modal, method: 'show'))
            {
                $modal->show();
            }
        }
    }

    public function close(): void
    {
        $this->reset();
    }

    public function save(SaveAction $action): void
    {
        if (! $this->generated instanceof Generated)
        {
            return;
        }

        if (! $this->canPerformAction(ability: 'update'))
        {
            return;
        }

        $action->handle(
            asset    : $this->generated,
            html     : $this->generatedContentHtml ?? '',
            callback : fn (string $text) => $this->saveSuccess(text: $text)
        );
    }

    public function download(DownloadAction $action): ?BinaryFileResponse
    {
        if (! $this->generated instanceof Generated)
        {
            $this->notifyError(
                message: trans(key: 'generator.download.failed')
            );

            return null;
        }

        if (! $this->canPerformAction(ability: 'view'))
        {
            return null;
        }

        return $action->handle(generated: $this->generated);
    }

    private function saveSuccess(string $text): void
    {
        $this->generatedContentText = $text;

        $this->notifySuccess(
            message: trans(key: 'letter.result.actions.saved')
        );
    }

    private function canPerformAction(string $ability): bool
    {
        if (! Gate::allows(ability: $ability, arguments: $this->generated))
        {
            $this->notifyError(
                message: trans(key: 'misc.action.disallowed')
            );

            return false;
        }

        return true;
    }
}
