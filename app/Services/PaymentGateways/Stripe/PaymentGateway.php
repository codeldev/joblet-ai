<?php

declare(strict_types=1);

namespace App\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentGatewayInterface;
use App\Enums\ProductPackageEnum;
use App\Models\User;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

final readonly class PaymentGateway implements PaymentGatewayInterface
{
    public function __construct(private ProductPackageEnum $package, private User $user) {}

    public function process(): ?string
    {
        try
        {
            $apiKey = $this->getApiKey();

            Stripe::setApiKey(apiKey: $apiKey);

            $checkout = Session::create(
                params: $this->buildData()
            );

            /** @var string $url */
            $url = $checkout->url;

            return $url;
        }
        catch (ApiErrorException | RuntimeException $e)
        {
            report(exception: $e);

            return null;
        }
    }

    /** @return array<string, mixed> */
    public function buildData(): array
    {
        $token = $this->generatePaymentToken();

        return [
            'line_items'          => $this->buildLineItems(),
            'metadata'            => $this->buildMetaData(token: $token),
            'client_reference_id' => $this->user->id,
            'customer_email'      => $this->user->email,
            'mode'                => 'payment',
            'currency'            => config(key: 'cashier.currency'),
            'cancel_url'          => $this->cancelUrl(),
            'success_url'         => $this->successUrl(token: $token),
        ];
    }

    public function generatePaymentToken(): string
    {
        return collect(value: [
            $this->user->id,
            $this->package->value,
            time(),
        ])->implode(value: '_');
    }

    public function paymentProcessor(): string
    {
        return 'stripe';
    }

    /** @throws RuntimeException */
    private function getApiKey(): string
    {
        $apiKey = config(key: 'cashier.secret');

        if (! is_string(value: $apiKey))
        {
            throw new RuntimeException(message: 'Invalid Stripe API key');
        }

        return $apiKey;
    }

    /** @return array<int, array<string, mixed>> */
    private function buildLineItems(): array
    {
        return [
            [
                'price'    => $this->package->stripeId(),
                'quantity' => 1,
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function buildMetaData(string $token): array
    {
        return [
            'package' => $this->package->value,
            'user'    => $this->user->id,
            'token'   => $token,
        ];
    }

    private function successUrl(string $token): string
    {
        return route(name: 'payment.success', parameters: [
            'gateway' => $this->paymentProcessor(),
            'token'   => $token,
        ]);
    }

    private function cancelUrl(): string
    {
        return route(
            name      : 'payment.cancel',
            parameters: $this->paymentProcessor()
        );
    }
}
