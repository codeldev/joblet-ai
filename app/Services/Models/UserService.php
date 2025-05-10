<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\User;

final class UserService
{
    public static function getTotalCredits(): int
    {
        return once(callback: static function (): int
        {
            /** @var User|null $user */
            $user = auth()->user();

            if (! $user instanceof User)
            {
                return 0;
            }

            return (int) $user->orders()->sum(column: 'tokens');
        });
    }

    public static function getUsedCredits(): int
    {
        return once(callback: static function (): int
        {
            /** @var User|null $user */
            $user = auth()->user();

            if (! $user instanceof User)
            {
                return 0;
            }

            return $user->usage()->count();
        });
    }

    public static function getRemainingCredits(): int
    {
        return once(static fn (): int => self::getTotalCredits() - self::getUsedCredits());
    }

    public static function getSupportUser(): User
    {
        /** @var string $email */
        $email = config(key: 'settings.contact');
        $user  = User::withoutGlobalScopes()->where(
            column  : 'email',
            operator: '=',
            value   : $email
        )->first();

        if ($user instanceof User)
        {
            return $user;
        }

        $user        = new User;
        $user->email = $email;

        return $user;
    }
}
