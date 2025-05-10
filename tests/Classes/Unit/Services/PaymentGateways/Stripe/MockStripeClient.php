<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use Stripe\Charge;
use Stripe\PaymentIntent;

final class MockStripeClient
{
    public function __construct(
        private readonly ?PaymentIntent $paymentIntent = null,
        private readonly ?Charge $charge = null
    ) {}

    public function __get(string $name)
    {
        if ($name === 'paymentIntents')
        {
            return new class($this->paymentIntent)
            {
                public function __construct(private readonly ?PaymentIntent $paymentIntent) {}

                public function retrieve($id, $params = null, $opts = null)
                {
                    return $this->paymentIntent;
                }
            };
        }

        if ($name === 'charges')
        {
            return new class($this->charge)
            {
                public function __construct(private readonly ?Charge $charge) {}

                public function retrieve($id, $params = null, $opts = null)
                {
                    return $this->charge;
                }
            };
        }

        return null;
    }
}
