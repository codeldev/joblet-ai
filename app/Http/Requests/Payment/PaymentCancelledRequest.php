<?php

declare(strict_types=1);

namespace App\Http\Requests\Payment;

use App\Contracts\Http\Requests\Payment\PaymentRequestInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

final class PaymentCancelledRequest extends FormRequest implements PaymentRequestInterface
{
    public function __invoke(): RedirectResponse
    {
        Session::put('payment-cancelled', true);

        return redirect()->route(route: 'account');
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }
}
