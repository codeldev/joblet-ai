<?php

declare(strict_types=1);

namespace App\Exceptions\Payment;

use Exception;

final class InvalidPaymentGatewayException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans(key: 'exception.payment.gateway'));
    }
}
