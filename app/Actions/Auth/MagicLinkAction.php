<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;

final class MagicLinkAction
{
    /** @param array<string, string> $validated */
    public function handle(array $validated, callable $callback): void
    {
        if (($user = $this->getUser(email: $validated['email'])) instanceof User)
        {
            $user->sendLoginLinkNotification();
        }

        $callback();
    }

    private function getUser(string $email): ?User
    {
        return User::query()->where(
            column  : 'email',
            operator: '=',
            value   : $email
        )->first();
    }
}
