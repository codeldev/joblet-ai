<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Concerns\HasThrottlingTrait;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Index extends Component
{
    use HasThrottlingTrait;

    public string $type = 'sign-in';

    public function mount(): void
    {
        $this->setupProperties(
            keyPrefix : 'auth',
            redirect  : 'auth'
        );

        $this->checkLockedOutOnMount();
    }

    #[Layout('components.layouts.auth')]
    public function render(): View
    {
        return view(view: 'livewire.auth.index')
            ->title(title: trans(key: 'auth.title'))
            ->layoutData(data: [
                'description' => trans(key: 'auth.description'),
            ]);
    }

    public function updatedType(): void
    {
        $event = $this->type === 'sign-in'
            ? 'reset-sign-up'
            : 'reset-sign-in';

        $this->dispatch(event: $event);
    }

    public function lockoutTimer(): void
    {
        $this->isLockedOut()
            ? $this->setLockoutMessage()
            : $this->redirectRoute(name: 'auth');
    }
}
