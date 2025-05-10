<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Concerns\HasAppMessagesTrait;
use App\Models\Generated;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Index extends Component
{
    use HasAppMessagesTrait;

    public function render(): View
    {
        return view(view: 'livewire.dashboard.index')
            ->title(title: trans(key: 'dashboard.title'))
            ->layoutData(data: [
                'description' => trans(key: 'dashboard.description'),
            ]);
    }

    /** @return Collection<int, Generated> */
    #[Computed]
    public function generations(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->generated()
            ->latest()
            ->get();
    }

    #[On('reload-dashboard')]
    public function reloadDashboard(): void
    {
        $this->dispatch(event: '$refresh');
    }
}
