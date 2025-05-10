<?php

declare(strict_types=1);

namespace App\Livewire\Contact;

use App\Concerns\HasNotificationsTrait;
use App\Contracts\Actions\Contact\SendContactActionInterface;
use App\Livewire\Forms\Contact\ContactForm;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

final class Index extends Component
{
    use HasNotificationsTrait;

    public ContactForm $form;

    #[Url]
    public int $contact = 0;

    public function mount(): void
    {
        $this->fillForm();
    }

    public function onLoad(): void
    {
        if ($this->contact === 1)
        {
            /** @var object $modal */
            $modal = Flux::modal(name: 'contact-form');

            if (method_exists(object_or_class: $modal, method: 'show'))
            {
                $modal->show();
            }
        }
    }

    public function render(): View
    {
        return view(view: 'livewire.contact.index');
    }

    public function submit(SendContactActionInterface $action): void
    {
        /** @var array<string,string> $validated */
        $validated = $this->form->validate();

        $action->handle(
            validated: $validated,
            success  : fn () => $this->messageSent(),
            failed   : fn () => $this->notifyError(
                message: trans(key: 'messages.contact.failed')
            )
        );
    }

    public function close(): void
    {
        $this->fillForm();
    }

    private function messageSent(): void
    {
        /** @var object $modal */
        $modal = Flux::modal(name: 'contact-form');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->fillForm();

        $this->notifySuccess(
            message: trans(key: 'messages.contact.success')
        );
    }

    private function fillForm(): void
    {
        $this->form->reset();
        $this->form->clearValidation();

        if (Auth::check())
        {
            $this->form->fill(values: auth()->user());
        }
    }
}
