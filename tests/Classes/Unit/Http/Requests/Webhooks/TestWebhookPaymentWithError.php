<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;

final class TestWebhookPaymentWithError implements PaymentWebhookInterface
{
    private ?string $error = 'Test error message';

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getPaymentData(): ?array
    {
        return null;
    }
}
