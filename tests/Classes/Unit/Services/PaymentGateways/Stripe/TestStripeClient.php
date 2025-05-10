<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use Stripe\Charge;
use Stripe\PaymentIntent;

final class TestStripeClient
{
    public $paymentIntents;

    public $charges;

    public function __construct(
        private readonly ?PaymentIntent $paymentIntent = null,
        private readonly ?Charge $charge = null
    ) {
        $this->paymentIntents = new class($this->paymentIntent)
        {
            public function __construct(private readonly ?PaymentIntent $paymentIntent) {}

            public function retrieve($id, $params = null, $opts = null)
            {
                return $this->paymentIntent;
            }
        };

        $this->charges = new class($this->charge)
        {
            public function __construct(private readonly ?Charge $charge) {}

            public function retrieve($id, $params = null, $opts = null)
            {
                return $this->charge;
            }
        };
    }
}
