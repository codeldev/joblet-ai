<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Actions\Account\LogoutAction;
use App\Concerns\HasNotificationsTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Logout extends Component
{
    use HasNotificationsTrait;

    public function render(): View
    {
        return view(view: 'livewire.account.logout');
    }

    public function submit(LogoutAction $action): void
    {
        $action->handle();

        $this->notifySuccess(
            message  : trans(key: 'account.logout.success'),
            redirect : route(name: 'auth')
        );
    }
}
