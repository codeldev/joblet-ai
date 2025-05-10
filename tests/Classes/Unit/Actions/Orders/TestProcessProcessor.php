<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Actions\Orders;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;

final class TestProcessProcessor implements PaymentProcessorInterface
{
    private ?string $error = null;

    public function getChargeData(): ?array
    {
        $package = ProductPackageEnum::STANDARD;

        return [
            'gateway' => 'stripe',
            'user'    => auth()->user(),
            'package' => $package,
            'amount'  => $package->price(),
        ];
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
