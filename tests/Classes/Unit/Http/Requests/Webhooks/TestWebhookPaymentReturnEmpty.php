<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;

final class TestWebhookPaymentReturnEmpty implements PaymentWebhookInterface
{
    private ?string $error = 'Test error';

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getPaymentData(): ?array
    {
        return [];
    }
}
