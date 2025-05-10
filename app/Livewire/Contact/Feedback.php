<?php

declare(strict_types=1);

namespace App\Livewire\Contact;

use App\Concerns\HasNotificationsTrait;
use App\Contracts\Actions\Contact\SendFeedbackActionInterface;
use App\Livewire\Forms\Contact\FeedbackForm;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

final class Feedback extends Component
{
    use HasNotificationsTrait;

    public FeedbackForm $form;

    public function mount(): void
    {
        $this->fillForm();
    }

    public function render(): View
    {
        return view(view: 'livewire.contact.feedback');
    }

    public function submit(SendFeedbackActionInterface $action): void
    {
        /** @var array<string,string> $validated */
        $validated = $this->form->validate();

        $action->handle(
            validated: $validated,
            success  : fn () => $this->feedbackSent(),
            failed   : fn () => $this->notifyError(
                message: trans(key: 'messages.feedback.failed')
            )
        );
    }

    public function close(): void
    {
        $this->fillForm();
    }

    private function feedbackSent(): void
    {
        /** @var object $modal */
        $modal = Flux::modal(name: 'feedback-form');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->fillForm();

        $this->notifySuccess(
            message: trans(key: 'messages.feedback.success')
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
