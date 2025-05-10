<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\SignUpAction;
use App\Concerns\HasNotificationsTrait;
use App\Concerns\HasThrottlingTrait;
use App\Livewire\Forms\Auth\SignUpForm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class SignUp extends Component
{
    use HasNotificationsTrait;

    use HasThrottlingTrait;

    public SignUpForm $form;

    public function mount(): void
    {
        $this->setupProperties(
            keyPrefix : 'signup',
            redirect  : 'auth',
        );
    }

    public function render(): View
    {
        return view(view: 'livewire.auth.signup');
    }

    public function submit(SignUpAction $action): void
    {
        if (! $this->throttled())
        {
            $this->form->clearValidation();

            /** @var array<string, mixed> $validated */
            $validated = $this->form->validate();

            $action->handle(
                validated: $validated,
                success  : fn () => $this->signupSuccess(),
                failed   : fn () => $this->signupFailed()
            );
        }
    }

    #[On('reset-sign-up')]
    public function clear(): void
    {
        $this->form->clearForm();
    }

    private function signupSuccess(): void
    {
        $this->notifySuccess(
            message  : trans(key: 'auth.sign.up.success'),
            redirect : route(name: 'dashboard')
        );
    }

    private function signupFailed(): void
    {
        $this->notifyError(
            message: trans(key: 'auth.sign.up.failed'),
        );
    }

    private function throttled(): bool
    {
        if ($this->isLockedOut())
        {
            $this->setLockoutMessage('auth.sign.up.lockout');

            $this->notifyError(
                message: $this->lockoutMessage ?? trans(key: 'auth.error')
            );

            return true;
        }

        $this->addLimiterHit(decayTime: 60 * 60);

        return false;
    }
}
