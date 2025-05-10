<?php

/** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace Tests\Classes\Vendor\Stripe;

use Override;
use RuntimeException;
use stdClass;
use Stripe\StripeClient;

final class MockStripeClientWithChargeException extends StripeClient
{
    private stdClass $paymentIntent;

    private string $paymentIntentId;

    private string $chargeId;

    private string $exceptionMessage;

    public function __construct(
        stdClass $paymentIntent,
        string $paymentIntentId,
        string $chargeId,
        string $exceptionMessage = 'Failed to retrieve charge'
    ) {
        $this->paymentIntent    = $paymentIntent;
        $this->paymentIntentId  = $paymentIntentId;
        $this->chargeId         = $chargeId;
        $this->exceptionMessage = $exceptionMessage;
    }

    #[Override]
    public function __get($name)
    {
        return $this;
    }

    /** @throws RuntimeException */
    public function retrieve(string $id): mixed
    {
        if ($id === $this->paymentIntentId)
        {
            return $this->paymentIntent;
        }

        if ($id === $this->chargeId)
        {
            throw new RuntimeException(message: $this->exceptionMessage);
        }

        return null;
    }
}
