<?php

/** @noinspection PhpPossiblePolymorphicInvocationInspection */

declare(strict_types=1);

namespace App\Actions\Account;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

final class ClearSessionAction
{
    /** @param array<string, string> $validated */
    public function handle(array $validated): void
    {
        $this
            ->signoutOtherDevices(password: $validated['password'])
            ->deleteOtherSessionRecords();
    }

    private function signoutOtherDevices(string $password): self
    {
        try
        {
            /** @var string $guardName */
            $guardName = config(key: 'browser-sessions.browser_session_guard');

            /** @var SessionGuard $guard */
            $guard = Auth::guard(name: $guardName);

            $guard->logoutOtherDevices(password: $password);
        }
        catch (AuthenticationException $e)
        {
            report(exception: $e);
        }

        return $this;
    }

    private function deleteOtherSessionRecords(): void
    {
        if ($user = Auth::user())
        {
            DB::table(table: 'sessions')
                ->where(column: 'user_id', operator: '=', value: $user->getAuthIdentifier())
                ->where(column: 'id', operator: '!=', value: Session::getId())
                ->delete();
        }
    }
}
