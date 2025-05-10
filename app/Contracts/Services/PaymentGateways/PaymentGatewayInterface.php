<?php

declare(strict_types=1);

namespace App\Contracts\Services\PaymentGateways;

use App\Enums\ProductPackageEnum;
use App\Models\User;

interface PaymentGatewayInterface
{
    public function __construct(ProductPackageEnum $package, User $user);

    public function process(): ?string;

    /** @return array<string, mixed> */
    public function buildData(): array;

    public function generatePaymentToken(): string;

    public function paymentProcessor(): string;
}
