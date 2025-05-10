<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Services\PaymentGateways\Stripe\PaymentProcessor;
use Illuminate\Support\ServiceProvider;

final class TestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: PaymentProcessorInterface::class,
            concrete: PaymentProcessor::class
        );
    }
}
