<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Payment;

use App\Contracts\Services\PaymentGateways\PaymentGatewayInterface;
use App\Enums\ProductPackageEnum;

final readonly class TestProcessorWithNullReturnProcess implements PaymentGatewayInterface
{
    public function __construct(
        public ProductPackageEnum $package,
        public ?object $user
    ) {}

    public function process(): ?string
    {
        return null;
    }

    /** @return array<string, mixed> */
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
