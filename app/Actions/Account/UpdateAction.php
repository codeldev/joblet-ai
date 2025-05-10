<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateAction
{
    /** @param array<string, string> $validated */
    public function handle(array $validated, callable $success, callable $failed): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->updateAccount(validated: $validated, user: $user)
            ? $success()
            : $failed();
    }

    /** @param array<string, string> $validated */
    private function updateAccount(array $validated, User $user): bool
    {
        try
        {
            DB::transaction(
                callback: static fn () => $user->update(attributes: $validated)
            );

            return true;
        }
        catch (Throwable $e)
        {
            report(exception: $e);

            return false;
        }
    }
}
