<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class PasswordAction
{
    public function handle(string $password, callable $success): void
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update(attributes: [
            'password' => Hash::make(value: $password),
        ]);

        $success();
    }
}
