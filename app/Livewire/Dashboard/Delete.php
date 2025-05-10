<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Concerns\HasNotifiableEventsTrait;
use App\Concerns\HasNotificationsTrait;
use App\Models\Generated;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

final class Delete extends Component
{
    use HasNotifiableEventsTrait;

    use HasNotificationsTrait;

    public ?Generated $generated = null;

    public function render(): View
    {
        return view(view: 'livewire.dashboard.delete');
    }

    #[On('dashboard-confirm-delete')]
    public function confirm(Generated $generated): void
    {
        $this->generated = $generated;

        /** @var object $modal */
        $modal = Flux::modal(name: 'confirm-deletable');

        if (method_exists(object_or_class: $modal, method: 'show'))
        {
            $modal->show();
        }
    }

    public function delete(): void
    {
        Gate::allows(ability: 'delete', arguments: $this->generated)
            ? $this->deletionAllowed()
            : $this->permissionDenied();
    }

    public function cancel(): void
    {
        $this->generated = null;
    }

    private function permissionDenied(): void
    {
        $this->notifyError(
            message: trans(key: 'misc.action.disallowed')
        );

        $this->generated = null;

        /** @var object $modal */
        $modal = Flux::modal(name: 'confirm-deletable');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }
    }

    private function deletionAllowed(): void
    {
        if ($this->generated instanceof Generated)
        {
            $this->generated->delete();
        }

        $this->generated = null;

        /** @var object $modal */
        $modal = Flux::modal(name: 'confirm-deletable');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->notifyAndDispatch(
            message: trans(key: 'letter.delete.modal.success'),
            event  : 'reload-dashboard',
        );
    }
}
