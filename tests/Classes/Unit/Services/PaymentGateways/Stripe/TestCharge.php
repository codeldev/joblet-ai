<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use Stripe\Charge;

final class TestCharge extends Charge
{
    public string $id;

    public string $status;

    public int $amount;

    public string $payment_intent;

    public string $balance_transaction;

    public string $receipt_url;

    public object $payment_method_details;

    public static function createSuccessfulCharge(): self
    {
        $charge                         = new self();
        $charge->id                     = 'ch_' . uniqid(prefix: 'test', more_entropy: true);
        $charge->status                 = 'succeeded';
        $charge->amount                 = 1999;
        $charge->payment_intent         = 'pi_' . uniqid(prefix: 'test', more_entropy: true);
        $charge->balance_transaction    = 'txn_' . uniqid(prefix: 'test', more_entropy: true);
        $charge->receipt_url            = 'https://receipt.stripe.com/' . uniqid(prefix: 'test', more_entropy: true);
        $charge->payment_method_details = (object) [
            'card' => (object) [
                'brand' => 'visa',
                'last4' => '4242',
            ],
        ];

        return $charge;
    }

    public static function createFailedCharge(): self
    {
        $charge         = self::createSuccessfulCharge();
        $charge->status = 'failed';

        return $charge;
    }

    public static function createChargeWithoutCardDetails(): self
    {
        $charge                         = self::createSuccessfulCharge();
        $charge->payment_method_details = (object) [];

        return $charge;
    }
}
