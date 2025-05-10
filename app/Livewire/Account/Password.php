<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Actions\Account\PasswordAction;
use App\Concerns\HasNotificationsTrait;
use App\Livewire\Forms\Account\PasswordForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Password extends Component
{
    use HasNotificationsTrait;

    public PasswordForm $form;

    public function render(): View
    {
        return view(view: 'livewire.account.password');
    }

    public function submit(PasswordAction $action): void
    {
        $this->form->clearValidation();

        /** @var array<string,string> $validated */
        $validated = $this->form->validate();

        $action->handle(
            password: $validated['password'],
            success : function (): void
            {
                $this->form->reset();

                $this->notifySuccess(
                    message: trans(key: 'account.password.success')
                );
            }
        );
    }
}
