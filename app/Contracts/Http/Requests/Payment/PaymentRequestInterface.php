<?php

declare(strict_types=1);

namespace App\Contracts\Http\Requests\Payment;

use Illuminate\Http\RedirectResponse;

interface PaymentRequestInterface
{
    public function __invoke(): RedirectResponse;

    public function authorize(): bool;

    /** @return array<string, array<int, string>> */
    public function rules(): array;
}
