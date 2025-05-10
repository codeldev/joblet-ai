<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Contracts\Services\PaymentGateways\Stripe\StripeServiceInterface;
use App\Services\PaymentGateways\Stripe\PaymentProcessor;
use App\Services\PaymentGateways\Stripe\StripeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Override;

final class StripeServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: StripeServiceInterface::class,
            concrete: StripeService::class
        );

        $this->app->bind(
            abstract: PaymentProcessorInterface::class,
            concrete: function (Application $app, array $parameters = []): PaymentProcessor
            {
                /** @var array<string, mixed> $payload */
                $payload = $parameters['payload'] ?? [];

                /** @var StripeServiceInterface $stripeService */
                $stripeService = $app->make(abstract: StripeServiceInterface::class);

                return new PaymentProcessor(
                    payload      : $payload,
                    stripeService: $stripeService
                );
            }
        );
    }
}
