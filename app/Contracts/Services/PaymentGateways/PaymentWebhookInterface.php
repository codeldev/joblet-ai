<?php

declare(strict_types=1);

namespace App\Contracts\Services\PaymentGateways;

interface PaymentWebhookInterface
{
    /** @return array<string, mixed>|null */
    public function getPaymentData(): ?array;

    public function getError(): ?string;
}
