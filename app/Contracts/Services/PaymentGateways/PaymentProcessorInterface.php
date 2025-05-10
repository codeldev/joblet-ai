<?php

declare(strict_types=1);

namespace App\Contracts\Services\PaymentGateways;

interface PaymentProcessorInterface
{
    /** @return array<string, mixed>|null */
    public function getChargeData(): ?array;

    public function getError(): ?string;
}
