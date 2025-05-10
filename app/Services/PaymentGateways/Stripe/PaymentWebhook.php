<?php

declare(strict_types=1);

namespace App\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;
use App\Exceptions\Stripe\InvalidStripePayloadException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use JsonException;

final class PaymentWebhook implements PaymentWebhookInterface
{
    private ?string $error = null;

    public function __construct(private readonly string $stripePayload) {}

    public function getError(): ?string
    {
        return $this->error;
    }

    /** @return array<string, mixed>|null */
    public function getPaymentData(): ?array
    {
        try
        {
            if (($payload = $this->getStripePayload()) === null || $payload === [])
            {
                throw new InvalidStripePayloadException;
            }

            if ($payload['type'] !== 'checkout.session.completed')
            {
                throw new InvalidStripePayloadException;
            }

            return $this->getChargeData(payload: $payload);
        }
        catch (Exception $e)
        {
            $this->error = $e->getMessage();

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     *
     * @throws BindingResolutionException
     */
    private function getChargeData(array $payload): ?array
    {
        /** @var PaymentProcessorInterface $service */
        $service     = app()->make(PaymentProcessorInterface::class, ['payload' => $payload]);
        $paymentData = $service->getChargeData();

        if (notEmpty(value: $service->getError()))
        {
            $this->error = $service->getError();

            return null;
        }

        return $paymentData;
    }

    /** @return array<string, mixed>|null */
    private function getStripePayload(): ?array
    {
        try
        {
            $result = json_decode(
                json       : $this->stripePayload,
                associative: true,
                depth      : 500,
                flags      : JSON_THROW_ON_ERROR
            );

            if (! is_array(value: $result))
            {
                return null;
            }

            /** @var array<string, mixed> $result */
            return $result;
        }
        catch (JsonException $e)
        {
            report(exception: $e);

            return null;
        }
    }
}
