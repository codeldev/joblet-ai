<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Session;

trait HasAppMessagesTrait
{
    use HasNotificationsTrait;

    public function displayMessages(): void
    {
        if (Session::exists(key: 'app-message'))
        {
            $messenger = Session::get(key: 'app-message');

            Session::forget(keys: 'app-message');

            $message  = trans(key: $messenger['message']);
            $redirect = $messenger['redirect'] ?? null;

            match ($messenger['type'])
            {
                'success' => $this->notifySuccess(
                    message : $message,
                    redirect: $redirect
                ),
                'error' => $this->notifyError(
                    message : $message,
                    redirect: $redirect
                ),
                'warning' => $this->notifyWarning(
                    message : $message,
                    redirect: $redirect
                ),
                'info' => $this->notifyInfo(
                    message : $message,
                    redirect: $redirect
                ),
                default => null,
            };
        }
    }
}
