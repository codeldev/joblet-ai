<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Actions\Account\ClearSessionAction;
use App\Concerns\HasNotificationsTrait;
use App\Livewire\Forms\Account\SessionsForm;
use Cjmellor\BrowserSessions\Facades\BrowserSessions;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Sessions extends Component
{
    use HasNotificationsTrait;

    public SessionsForm $form;

    public function render(): View
    {
        return view(view: 'livewire.account.sessions');
    }

    /** @return Collection<int, object> */
    #[Computed]
    public function sessions(): Collection
    {
        return BrowserSessions::sessions()->map(
            callback: fn (mixed $session): object => $this->setDeviceType(session: (object) $session)
        );
    }

    public function submit(ClearSessionAction $action): void
    {
        /** @var array<string, string> $validated */
        $validated = $this->form->validate();

        $action->handle(validated: $validated);

        /** @var object $modal */
        $modal = Flux::modal(name: 'clear-sessions');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->notifySuccess(
            message: trans(key: 'account.sessions.cleared')
        );

        $this->form->clear();
    }

    public function cancel(): void
    {
        $this->form->clear();
    }

    private function setDeviceType(object $session): object
    {
        /** @var array{desktop: bool, mobile: bool, tablet: bool} $device */
        $device = $session->device ?? [];

        /** @phpstan-ignore-next-line */
        $session->deviceType = match (true)
        {
            $device['desktop'] => trans(key: 'account.sessions.device.desktop'),
            $device['mobile']  => trans(key: 'account.sessions.device.mobile'),
            $device['tablet']  => trans(key: 'account.sessions.device.tablet'),
            default            => trans(key: 'account.sessions.device.unknown'),
        };

        return $session;
    }
}
