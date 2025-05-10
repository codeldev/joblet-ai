<?php

declare(strict_types=1);

namespace App\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\Stripe\StripeServiceInterface;
use Exception;
use RuntimeException;
use Stripe\Charge;
use Stripe\StripeClient;

final class StripeService implements StripeServiceInterface
{
    private StripeClient $client;

    public function __construct(?StripeClient $client = null)
    {
        if ($client instanceof StripeClient)
        {
            $this->client = $client;

            return;
        }

        $stripeSecret = config(key: 'cashier.secret');

        if (! is_string($stripeSecret))
        {
            throw new RuntimeException(message: 'Stripe secret must be a string or a client must be provided');
        }

        $this->client = new StripeClient(config: $stripeSecret);
    }

    public function getChargeFromPaymentIntent(string $paymentIntentId): ?Charge
    {
        try
        {
            $intent = $this->client->paymentIntents->retrieve(id: $paymentIntentId);

            if (is_string(value: $intent->latest_charge))
            {
                return $this->client->charges->retrieve(id: $intent->latest_charge);
            }
        }
        catch (Exception $e)
        {
            report(exception: $e);
        }

        return null;
    }
}
