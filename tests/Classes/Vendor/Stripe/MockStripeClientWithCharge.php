<?php

/** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace Tests\Classes\Vendor\Stripe;

use Override;
use stdClass;
use Stripe\StripeClient;

final class MockStripeClientWithCharge extends StripeClient
{
    private stdClass $paymentIntent;

    private mixed $charge;

    private string $paymentIntentId;

    private string $chargeId;

    public function __construct(stdClass $paymentIntent, mixed $charge, string $paymentIntentId, string $chargeId)
    {
        $this->paymentIntent   = $paymentIntent;
        $this->charge          = $charge;
        $this->paymentIntentId = $paymentIntentId;
        $this->chargeId        = $chargeId;
    }

    #[Override]
    public function __get($name)
    {
        return $this;
    }

    public function retrieve(string $id): mixed
    {
        if ($id === $this->paymentIntentId)
        {
            return $this->paymentIntent;
        }

        if ($id === $this->chargeId)
        {
            return $this->charge;
        }

        return null;
    }
}
