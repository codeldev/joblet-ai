<?php

declare(strict_types=1);

namespace App\Concerns;

trait HasNotifiableEventsTrait
{
    use HasNotificationsTrait;

    protected function notifyAndDispatch(string $message, string $event): void
    {
        $this->notifySuccess(
            message: $message
        );

        $this->dispatch(
            event: $event
        );
    }
}
