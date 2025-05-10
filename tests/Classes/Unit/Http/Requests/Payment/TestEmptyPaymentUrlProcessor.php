<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Payment;

use App\Contracts\Services\PaymentGateways\PaymentGatewayInterface;
use App\Enums\ProductPackageEnum;

final readonly class TestEmptyPaymentUrlProcessor implements PaymentGatewayInterface
{
    public function __construct(
        private ProductPackageEnum $package,
        private mixed $user
    ) {}

    public function process(): string
    {
        return '';
    }

    public function buildData(): array
    {
        return [];
    }

    public function generatePaymentToken(): string
    {
        return 'test-token';
    }

    public function paymentProcessor(): string
    {
        return 'test-processor';
    }
}
