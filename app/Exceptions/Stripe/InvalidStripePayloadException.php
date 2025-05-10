<?php

declare(strict_types=1);

namespace App\Exceptions\Stripe;

use Exception;

final class InvalidStripePayloadException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'payment.webhook.error.payload.invalid'));
    }
}
