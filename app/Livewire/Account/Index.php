<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Concerns\HasAppMessagesTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Index extends Component
{
    use HasAppMessagesTrait;

    public string $tab = 'profile';

    public function render(): View
    {
        return view(view: 'livewire.account.index')
            ->title(title: trans(key: 'account.title'))
            ->layoutData(data: [
                'description' => trans(key: 'account.description'),
            ]);
    }
}
