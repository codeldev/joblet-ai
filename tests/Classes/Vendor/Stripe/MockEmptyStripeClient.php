<?php

/** @noinspection PhpUnusedParameterInspection */
/** @noinspection MagicMethodsValidityInspection */
/** @noinspection PhpMissingParentConstructorInspection */

declare(strict_types=1);

namespace Tests\Classes\Vendor\Stripe;

use Override;
use Stripe\StripeClient;

final class MockEmptyStripeClient extends StripeClient
{
    public function __construct() {}

    #[Override]
    public function __get($name)
    {
        return $this;
    }

    public function retrieve(string $id): null
    {
        return null;
    }
}
