<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

final class OrderPayment extends Component
{
    public bool $paymentCancelled = false;

    public bool $paymentSuccess = false;

    public ?string $paymentError = null;

    public bool $paymentComplete = false;

    public function mount(): void
    {
        if (Session::exists(key: 'payment-cancelled'))
        {
            $this->paymentCancelled = true;

            Session::forget(keys: 'payment-cancelled');
        }

        if (Session::exists(key: 'payment-token'))
        {
            $this->paymentSuccess = true;
        }

        if (Session::exists(key: 'payment-error'))
        {
            /** @var string $paymentError */
            $paymentError = Session::get(key: 'payment-error');

            $this->paymentError = $paymentError;

            Session::forget(keys: 'payment-error');
        }
    }

    #[On('reload-payment')]
    public function reloadPayment(): void
    {
        $this->reset();
    }

    public function render(): View
    {
        return view(view: 'livewire.account.payment');
    }

    public function validatePayment(): void
    {
        if ($this->paymentProcessed())
        {
            Session::forget(keys: 'payment-token');

            $this->paymentSuccess  = false;
            $this->paymentComplete = true;

            $this->dispatch(event: 'reload-account');
        }
    }

    private function paymentProcessed(): bool
    {
        return Payment::query()->where(
            column  : 'user_id',
            operator: '=',
            value   : auth()->id()
        )->where(
            column  : 'payment_token',
            operator: '=',
            value   : Session::get(key: 'payment-token')
        )->exists();
    }
}
