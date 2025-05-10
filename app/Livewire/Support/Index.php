<?php

declare(strict_types=1);

namespace App\Livewire\Support;

use App\Enums\ProductPackageEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Index extends Component
{
    public function render(): View
    {
        $viewData = [
            'contact' => config(key: 'settings.contact'),
            'appName' => config(key: 'app.name'),
        ];

        return view(view: 'livewire.support.index', data: $viewData)
            ->title(title: trans(key: 'support.title'))
            ->layoutData(data: [
                'description' => trans(key: 'support.description'),
            ]);
    }

    /** @return Collection<int, object> */
    #[Computed]
    public function packages(): Collection
    {
        return ProductPackageEnum::getAll();
    }
}
