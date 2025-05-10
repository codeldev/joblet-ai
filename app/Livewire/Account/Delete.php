<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Actions\Account\DeleteAction;
use App\Concerns\HasNotificationsTrait;
use App\Livewire\Forms\Account\DeleteForm;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Renderless;
use Livewire\Component;

final class Delete extends Component
{
    use HasNotificationsTrait;

    public DeleteForm $form;

    public function render(): View
    {
        return view(view: 'livewire.account.delete');
    }

    public function submit(DeleteAction $action): void
    {
        $this->form->clearValidation();

        $this->form->validate();

        $action->handle(
            success: fn () => $this->accountDeleted()
        );
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->form->clearValidation();
    }

    #[Renderless]
    private function accountDeleted(): void
    {
        /** @var object $modal */
        $modal = Flux::modal(name: 'confirm-deletion');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->notifySuccess(
            message : trans(key: 'account.delete.success'),
            redirect: route(name: 'home')
        );
    }
}
