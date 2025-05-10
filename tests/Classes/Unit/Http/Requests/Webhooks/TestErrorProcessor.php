<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;

final class TestErrorProcessor implements PaymentProcessorInterface
{
    public function getChargeData(): array
    {
        return [];
    }

    public function getError(): ?string
    {
        return 'processor error';
    }
}
