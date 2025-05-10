<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;

final class TestErrorWebhookProcessor implements PaymentProcessorInterface
{
    private string $error = 'Payment processor error';

    public function getChargeData(): ?array
    {
        return null;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
