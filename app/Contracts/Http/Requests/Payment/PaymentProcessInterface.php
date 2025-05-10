<?php

declare(strict_types=1);

namespace App\Contracts\Http\Requests\Payment;

use App\Enums\ProductPackageEnum;
use Illuminate\Http\RedirectResponse;

interface PaymentProcessInterface extends PaymentRequestInterface
{
    public function __invoke(): RedirectResponse;

    public function paymentError(string $message): RedirectResponse;

    public function getGateway(): string;

    public function getPackage(): ProductPackageEnum;
}
