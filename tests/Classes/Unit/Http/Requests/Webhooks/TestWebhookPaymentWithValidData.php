<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;
use App\Enums\ProductPackageEnum;
use App\Models\User;

final class TestWebhookPaymentWithValidData implements PaymentWebhookInterface
{
    private ?string $error = null;

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getPaymentData(): ?array
    {
        return [
            'user'    => new User,
            'package' => ProductPackageEnum::STANDARD,
            'amount'  => 100,
            'gateway' => 'stripe',
        ];
    }
}
