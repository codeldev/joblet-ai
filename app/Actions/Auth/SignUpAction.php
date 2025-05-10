<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class SignUpAction
{
    /**
     * @param  array<string, mixed>  $validated
     * @param  callable(): void  $success
     * @param  callable(): void  $failed
     */
    public function handle(array $validated, callable $success, callable $failed): void
    {
        $this->setUserData(
            validated : $validated,
            callback  : function (array $userData) use ($success, $failed): void
            {
                /** @var array<string,mixed> $userData */
                $this->createAccount(
                    userData : $userData,
                    success  : $success,
                    failed   : $failed
                );
            },
            error: fn () => $failed()
        );
    }

    /**
     * @param  array<string,mixed>  $userData
     * @param  callable(): void  $success
     * @param  callable(): void  $failed
     */
    private function createAccount(array $userData, callable $success, callable $failed): void
    {
        try
        {
            DB::transaction(
                callback: static fn () => Auth::login(
                    User::create(attributes: $userData)
                )
            );

            $success();
        }
        catch (Throwable $e)
        {
            report(exception: $e);

            $failed();
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  callable(array<string,mixed>): void  $callback
     * @param  callable(): void  $error
     */
    private function setUserData(array $validated, callable $callback, callable $error): void
    {
        try
        {
            /** @var array<string,string> $validated */
            $dataset = collect(value: $validated)
                ->except(keys: 'agreed')
                ->toArray();

            $dataset['password'] = Hash::make(value: $validated['password']);

            /** @var array<string,mixed> $callbackData */
            $callbackData = $dataset;

            $callback($callbackData);
        }
        catch (Throwable $e)
        {
            report(exception: $e);

            $error();
        }
    }
}
