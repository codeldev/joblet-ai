<?php

/** @noinspection PhpUndefinedFieldInspection */

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class MagicLoginLinkRequest extends FormRequest
{
    private ?User $user = null;

    public function __invoke(): RedirectResponse
    {
        $this->setUser();

        if (! $this->user instanceof User)
        {
            return $this->setErrorAndRedirect();
        }

        if (! $this->hashMatches())
        {
            return $this->setErrorAndRedirect();
        }

        return $this->loginUserAndRedirect();
    }

    public function authorize(): bool
    {
        return ! ($this->user() instanceof User);
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }

    private function setUser(): void
    {
        $this->user = User::where(
            column  : 'id',
            operator: '=',
            value   : $this->id
        )->first();
    }

    private function hashMatches(): bool
    {
        if ($this->user instanceof User)
        {
            return sha1(string: $this->user->email) === $this->hash;
        }

        return false;
    }

    private function setErrorAndRedirect(): RedirectResponse
    {
        Session::put('app-message', [
            'type'    => 'error',
            'message' => 'auth.sign.in.link.invalid',
        ]);

        return redirect()->route(route: 'home');
    }

    private function loginUserAndRedirect(): RedirectResponse
    {
        if ($this->user instanceof User)
        {
            Auth::login($this->user);

            Session::put('app-message', [
                'type'    => 'success',
                'message' => 'auth.sign.in.link.success',
            ]);

            return redirect()->route(route: 'dashboard');
        }

        return $this->setErrorAndRedirect();
    }
}
