<?php

declare(strict_types=1);

namespace App\Contracts\Services\PaymentGateways\Stripe;

use Stripe\Charge;

interface StripeServiceInterface
{
    public function getChargeFromPaymentIntent(string $paymentIntentId): ?Charge;
}
