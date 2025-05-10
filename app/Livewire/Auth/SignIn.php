<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\MagicLinkAction;
use App\Actions\Auth\SignInAction;
use App\Concerns\HasNotifiableEventsTrait;
use App\Concerns\HasThrottlingTrait;
use App\Livewire\Forms\Auth\SignInForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

final class SignIn extends Component
{
    use HasNotifiableEventsTrait;

    use HasThrottlingTrait;

    public SignInForm $form;

    public function mount(): void
    {
        $this->setupProperties(
            keyPrefix : 'auth',
            redirect  : 'auth'
        );

        $this->checkLockedOutOnMount();
    }

    public function render(): View
    {
        return view(view: 'livewire.auth.signin');
    }

    public function submit(SignInAction $action): void
    {
        $this->ensureIsNotRateLimited();

        $this->form->clearValidation();

        /** @var array<string, string> $credentials */
        $credentials = $this->form->validate();

        $action->handle(
            credentials: $credentials,
            success    : fn () => $this->loginSuccess(),
            failed     : fn () => $this->loginFailed()
        );
    }

    public function magicLink(MagicLinkAction $action): void
    {
        $this->ensureIsNotRateLimited();

        $this->form->clearValidation();

        /** @var array<string, string> $validated */
        $validated = $this->form->validateOnly(
            field: 'email'
        );

        $action->handle(
            validated: $validated,
            callback : fn () => $this->magicLinkSent()
        );
    }

    #[On('reset-sign-in')]
    public function clear(): void
    {
        $this->form->clearForm();
    }

    private function magicLinkSent(): void
    {
        $this->notifyAndDispatch(
            message : trans(key: 'auth.sign.in.forgot.sent'),
            event   : 'reset-sign-in'
        );
    }

    private function loginSuccess(): void
    {
        $this->clearRateLimits();

        Session::regenerate();

        $this->notifySuccess(
            message : trans(key: 'auth.sign.in.success'),
            redirect: route(name: 'dashboard')
        );
    }

    private function loginFailed(): void
    {
        $this->addLimiterHit();

        $this->notifyError(
            message: trans(key: 'auth.sign.in.failed')
        );
    }
}
