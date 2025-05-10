<?php

declare(strict_types=1);

namespace App\Actions\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class LogoutAction
{
    public function handle(): void
    {
        Auth::guard(name: 'web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
