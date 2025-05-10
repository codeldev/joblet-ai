<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use App\Services\PaymentGateways\Stripe\PaymentGateway;

it('returns null when an API error occurs', function (): void
{
    $this->mock(abstract: 'alias:Stripe\Checkout\Session', mock: function ($mock): void
    {
        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new RuntimeException(message: 'API Key Error'));
    });

    $gateway = new PaymentGateway(
        package: ProductPackageEnum::STANDARD,
        user   : testUser()
    );

    expect(value: $gateway->process())
        ->toBeNull();
})->group(groups: 'isolated');
