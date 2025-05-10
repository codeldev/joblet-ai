<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Jobs\DeleteAccountJob;

final class DeleteAction
{
    public function handle(callable $success): void
    {
        /** @var string $userId */
        $userId = auth()->id();

        if ($userId)
        {
            DeleteAccountJob::dispatch($userId);
        }

        (new LogoutAction)->handle();

        $success();
    }
}
