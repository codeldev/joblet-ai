<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;
use App\Models\User;

final class TestPaymentProcessor implements PaymentProcessorInterface
{
    private ?string $error = null;

    private array $chargeData;

    private bool $shouldReturnNull = false;

    public function __construct(
        private readonly User $user,
        private readonly ProductPackageEnum $package,
        private readonly string $eventId,
        private readonly string $paymentIntent,
        private readonly string $paymentToken,
        private readonly bool $hasCardDetails = true,
        private readonly bool $isSuccessful = true
    ) {
        $this->buildChargeData();
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getChargeData(): ?array
    {
        if ($this->shouldReturnNull)
        {
            return null;
        }

        return $this->chargeData;
    }

    public function setError(string $error): self
    {
        $this->error            = $error;
        $this->shouldReturnNull = true;

        return $this;
    }

    private function buildChargeData(): void
    {
        $chargeId           = 'ch_' . uniqid(prefix: 'test', more_entropy: true);
        $balanceTransaction = 'txn_' . uniqid(prefix: 'test', more_entropy: true);
        $receiptUrl         = 'https://receipt.stripe.com/' . uniqid(prefix: 'test', more_entropy: true);

        $this->chargeData = [
            'gateway'        => 'stripe',
            'user'           => $this->user,
            'package'        => $this->package,
            'amount'         => 1999,
            'card_type'      => $this->hasCardDetails ? 'visa' : null,
            'card_last4'     => $this->hasCardDetails ? '4242' : null,
            'event_id'       => $this->eventId,
            'intent_id'      => $this->paymentIntent,
            'charge_id'      => $chargeId,
            'transaction_id' => $balanceTransaction,
            'receipt_url'    => $receiptUrl,
            'payment_token'  => $this->paymentToken,
        ];
    }
}
