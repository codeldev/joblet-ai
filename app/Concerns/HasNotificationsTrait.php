<?php

declare(strict_types=1);

namespace App\Concerns;

use Flux\Flux;
use Livewire\Features\SupportEvents\HandlesEvents;

trait HasNotificationsTrait
{
    use HandlesEvents;

    public function notifySuccess(string $message, ?string $redirect = null): void
    {
        $this->dispatchNotification(
            type    : 'success',
            message : $message,
            redirect: $redirect
        );
    }

    public function notifyError(string $message, ?string $redirect = null): void
    {
        $this->dispatchNotification(
            type    : 'danger',
            message : $message,
            redirect: $redirect
        );
    }

    public function notifyInfo(string $message, ?string $redirect = null): void
    {
        $this->dispatchNotification(
            type    : 'info',
            message : $message,
            redirect: $redirect
        );
    }

    public function notifyWarning(string $message, ?string $redirect = null): void
    {
        $this->dispatchNotification(
            type    : 'warning',
            message : $message,
            redirect: $redirect
        );
    }

    protected function dispatchNotification(string $type, string $message, ?string $redirect = null): void
    {
        Flux::toast(
            text    : $message,
            duration: 3500,
            variant : $type,
        );

        $this->setRedirect(redirect: $redirect);
    }

    private function setRedirect(?string $redirect = null): void
    {
        if (! in_array(needle: $redirect, haystack: [null, '', '0'], strict: true))
        {
            $this->dispatch(
                event   : 'redirect',
                timeout : 3750,
                redirect: $redirect,
            );
        }
    }
}
