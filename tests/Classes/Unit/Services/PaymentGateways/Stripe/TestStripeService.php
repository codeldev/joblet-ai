<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\Stripe\StripeServiceInterface;
use RuntimeException;
use Stripe\Charge;

final class TestStripeService implements StripeServiceInterface
{
    public function __construct(
        private readonly ?Charge $charge = null,
        private readonly bool $throwException = false
    ) {}

    public function getChargeFromPaymentIntent(string $paymentIntentId): ?Charge
    {
        if ($this->throwException)
        {
            throw new RuntimeException('Stripe API error');
        }

        return $this->charge;
    }
}
