<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;

final class TestWebhookProcessor implements PaymentProcessorInterface
{
    private ?string $error = null;

    public function getChargeData(): ?array
    {
        return [
            'gateway' => 'stripe',
            'user'    => 'test-user',
            'package' => 'test-package',
            'amount'  => 1999,
        ];
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
