<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Actions\Account\UpdateAction;
use App\Concerns\HasNotificationsTrait;
use App\Livewire\Forms\Account\ProfileForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Profile extends Component
{
    use HasNotificationsTrait;

    public ProfileForm $form;

    public function mount(): void
    {
        $this->form->fill(values: auth()->user());
    }

    public function render(): View
    {
        return view(view: 'livewire.account.profile');
    }

    public function submit(UpdateAction $action): void
    {
        /** @var array<string, string> $validated */
        $validated = $this->form->validate();

        $action->handle(
            validated: $validated,
            success  : fn () => $this->notifySuccess(
                message: trans(key: 'account.profile.success')
            ),
            failed   : fn () => $this->notifyError(
                message: trans(key: 'account.profile.failed')
            )
        );
    }
}
