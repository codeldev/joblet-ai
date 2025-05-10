<?php

/** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace Tests\Classes\Vendor\Stripe;

use Override;
use RuntimeException;
use Stripe\StripeClient;

final class MockStripeClientWithException extends StripeClient
{
    private string $paymentIntentId;

    private string $exceptionMessage;

    public function __construct(string $paymentIntentId, string $exceptionMessage = 'Stripe API error')
    {
        $this->paymentIntentId  = $paymentIntentId;
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
            throw new RuntimeException(message: $this->exceptionMessage);
        }

        return null;
    }
}
