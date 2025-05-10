<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use Stripe\PaymentIntent;

final class TestPaymentIntent extends PaymentIntent
{
    public ?string $latest_charge;

    public static function createWithCharge(): self
    {
        $intent                = new self();
        $intent->latest_charge = 'ch_' . uniqid(prefix: 'test', more_entropy: true);

        return $intent;
    }

    public static function createWithoutCharge(): self
    {
        $intent                = new self();
        $intent->latest_charge = null;

        return $intent;
    }
}
