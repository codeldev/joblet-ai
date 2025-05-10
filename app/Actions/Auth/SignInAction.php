<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

final class SignInAction
{
    /** @param array<string, string> $credentials */
    public function handle(
        array $credentials,
        callable $success,
        callable $failed
    ): void {
        $signInWith = collect(value: $credentials)
            ->except(keys: 'remember')
            ->toArray();

        /** @var bool $remember */
        $remember = $credentials['remember'];

        Auth::attempt($signInWith, $remember)
            ? $success()
            : $failed();
    }
}
