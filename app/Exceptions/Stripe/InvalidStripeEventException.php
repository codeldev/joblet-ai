<?php

declare(strict_types=1);

namespace App\Exceptions\Stripe;

use Exception;

final class InvalidStripeEventException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'exception.stripe.invalid.event'));
    }
}
