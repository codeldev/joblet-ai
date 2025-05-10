<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Concerns\HasNotificationsTrait;
use App\Models\Generated;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

final class Settings extends Component
{
    use HasNotificationsTrait;

    public ?Generated $generated = null;

    public string $tab = 'letter-settings';

    public function render(): View
    {
        return view(view: 'livewire.dashboard.settings');
    }

    #[On('dashboard-view-settings')]
    public function view(Generated $generated): void
    {
        $this->generated = $generated;

        Gate::allows(ability: 'view', arguments: $generated)
            ? $this->show()
            : $this->notifyError(
                message: trans(key: 'misc.action.disallowed')
            );
    }

    public function close(): void
    {
        $this->reset();
    }

    private function show(): void
    {
        /** @var object $modal */
        $modal = Flux::modal(name: 'show-settings');

        if (method_exists(object_or_class: $modal, method: 'show'))
        {
            $modal->show();
        }
    }
}
