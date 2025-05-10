<?php

/** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace Tests\Classes\Vendor\Stripe;

use Override;
use stdClass;
use Stripe\StripeClient;

final class MockStripeClientWithNonStringCharge extends StripeClient
{
    private stdClass $paymentIntent;

    private string $paymentIntentId;

    public function __construct(stdClass $paymentIntent, string $paymentIntentId)
    {
        $this->paymentIntent   = $paymentIntent;
        $this->paymentIntentId = $paymentIntentId;
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

        return null;
    }
}
