<?php

declare(strict_types=1);

namespace App\Livewire\Home;

use App\Concerns\HasAppMessagesTrait;
use App\Contracts\Services\Home\LetterServiceInterface;
use App\Enums\ProductPackageEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Index extends Component
{
    use HasAppMessagesTrait;

    public function mount(): void
    {
        if (Auth::check())
        {
            $this->redirectRoute(name: 'dashboard');
        }
    }

    #[Layout('components.layouts.home')]
    public function render(): View
    {
        return view(view: 'livewire.home.index', data: $this->viewData())
            ->title(title: trans(key: 'home.title'))
            ->layoutData(data: [
                'description' => trans(key: 'home.description'),
            ]);
    }

    /** @return array<string,mixed> */
    private function viewData(): array
    {
        /** @var Collection<int, array<string, mixed>> $letters */
        $letters = Cache::remember(
            key     : 'home-letters',
            ttl     : now()->addHours(value: 3),
            callback: static fn (): Collection => app()->make(abstract: LetterServiceInterface::class)->get()
        );

        return [
            'appName'  => config(key: 'app.name'),
            'packages' => ProductPackageEnum::getAll(),
            'letters'  => $letters,
        ];
    }
}
