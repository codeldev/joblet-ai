<?php

declare(strict_types=1);

namespace App\Livewire\Privacy;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Index extends Component
{
    public function render(): View
    {
        $viewData = [
            'contact' => config(key: 'settings.contact'),
        ];

        return view(view: 'livewire.privacy.index', data: $viewData)
            ->title(title: trans(key: 'privacy.title'))
            ->layoutData(data: [
                'description' => trans(key: 'privacy.description'),
            ]);
    }
}
