<?php

declare(strict_types=1);

namespace App\Services\PaymentGateways\Stripe;

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Contracts\Services\PaymentGateways\Stripe\StripeServiceInterface;
use App\Enums\ProductPackageEnum;
use App\Exceptions\Product\InvalidProductPackageException;
use App\Exceptions\Stripe\InvalidStripeChargeException;
use App\Exceptions\Stripe\InvalidStripeEventException;
use App\Exceptions\Stripe\InvalidStripeIntentException;
use App\Exceptions\Stripe\InvalidStripeTokenException;
use App\Exceptions\Stripe\InvalidStripeUserException;
use App\Exceptions\Stripe\StripePaymentFailedException;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Stripe\Charge;

final class PaymentProcessor implements PaymentProcessorInterface
{
    private ?string $error                = null;

    private ?string $eventId             = null;

    private ?string $paymentIntent       = null;

    private ?User $user                  = null;

    private ?ProductPackageEnum $package = null;

    private ?string $paymentToken         = null;

    private ?Charge $charge               = null;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        private readonly array $payload,
        private readonly StripeServiceInterface $stripeService
    ) {}

    public function getError(): ?string
    {
        return $this->error;
    }

    /** @return array<string, mixed>|null */
    public function getChargeData(): ?array
    {
        try
        {
            $this->setEventId();
            $this->setUser();
            $this->setPackage();
            $this->setPaymentToken();
            $this->setPaymentIntentId();
            $this->setPaymentCharge();
            $this->validatePaymentData();

            return $this->buildPaymentData();
        }
        catch (Exception $e)
        {
            $this->error = $e->getMessage();

            report(exception: $e);

            return null;
        }
    }

    private function setEventId(): void
    {
        $eventId = Arr::get(array: $this->payload, key: 'id');

        $this->eventId = is_string(value: $eventId)
            ? $eventId
            : null;
    }

    private function setUser(): void
    {
        $userId = Arr::get(
            array: $this->payload,
            key: 'data.object.client_reference_id'
        );

        if ($userId !== null)
        {
            $user = User::find(id: $userId);

            if ($user instanceof User)
            {
                $this->user = $user;
            }
        }
    }

    private function setPackage(): void
    {
        $packageId = Arr::get(
            array: $this->payload,
            key: 'data.object.metadata.package'
        );

        if (is_numeric(value: $packageId))
        {
            $this->package = ProductPackageEnum::from(value: (int) $packageId);
        }
    }

    private function setPaymentToken(): void
    {
        $token = Arr::get(
            array: $this->payload,
            key: 'data.object.metadata.token'
        );

        $this->paymentToken = is_string(value: $token)
            ? $token
            : null;
    }

    private function setPaymentIntentId(): void
    {
        $intent = Arr::get(
            array: $this->payload,
            key: 'data.object.payment_intent'
        );

        $this->paymentIntent = is_string(value: $intent)
            ? $intent
            : null;
    }

    private function setPaymentCharge(): void
    {
        if ($this->paymentIntent === null)
        {
            return;
        }

        $this->charge = $this->stripeService->getChargeFromPaymentIntent(
            paymentIntentId: $this->paymentIntent
        );
    }

    /** @return array<string, mixed> */
    private function buildPaymentData(): array
    {
        $charge = $this->charge;

        assert(assertion: $charge instanceof Charge);

        $card = $this->getCardDetails(charge: $charge);

        return [
            'gateway'        => 'stripe',
            'user'           => $this->user,
            'package'        => $this->package,
            'amount'         => $charge->amount ?? 0,
            'card_type'      => $card->type,
            'card_last4'     => $card->last4,
            'event_id'       => $this->eventId,
            'intent_id'      => $charge->payment_intent      ?? null,
            'charge_id'      => $charge->id                  ?? null,
            'transaction_id' => $charge->balance_transaction ?? null,
            'receipt_url'    => $charge->receipt_url         ?? null,
            'payment_token'  => $this->paymentToken,
        ];
    }

    /** @return object{type: ?string, last4: ?string} */
    private function getCardDetails(Charge $charge): object
    {
        $cardType  = null;
        $cardLast4 = null;

        if (
            isset($charge->payment_method_details->card)
            && is_object(value: $charge->payment_method_details->card)
        ) {
            /** @var null|string $cardType */
            $cardType = $charge->payment_method_details->card->brand ?? null;

            /** @var null|string $cardLast4 */
            $cardLast4 = $charge->payment_method_details->card->last4 ?? null;
        }

        return (object) [
            'type'  => $cardType,
            'last4' => $cardLast4,
        ];
    }

    /**
     * @throws InvalidStripeIntentException
     * @throws InvalidStripeTokenException
     * @throws InvalidStripeEventException
     * @throws StripePaymentFailedException
     * @throws InvalidStripeChargeException
     * @throws InvalidStripeUserException
     * @throws InvalidProductPackageException
     */
    private function validatePaymentData(): void
    {
        $this->validateEventId();
        $this->validateUser();
        $this->validatePaymentIntent();
        $this->validatePackage();
        $this->validatePaymentToken();
        $this->validateCharge();
        $this->validateChargeStatus();
    }

    /**
     * @throws InvalidStripeEventException
     */
    private function validateEventId(): void
    {
        if (! notEmpty(value: $this->eventId))
        {
            throw new InvalidStripeEventException;
        }
    }

    /**
     * @throws InvalidStripeUserException
     */
    private function validateUser(): void
    {
        if (! $this->user instanceof User)
        {
            throw new InvalidStripeUserException;
        }
    }

    /**
     * @throws InvalidStripeIntentException
     */
    private function validatePaymentIntent(): void
    {
        if (! notEmpty(value: $this->paymentIntent))
        {
            throw new InvalidStripeIntentException;
        }
    }

    /**
     * @throws InvalidProductPackageException
     */
    private function validatePackage(): void
    {
        if (! $this->package instanceof ProductPackageEnum)
        {
            throw new InvalidProductPackageException;
        }
    }

    /**
     * @throws InvalidStripeTokenException
     */
    private function validatePaymentToken(): void
    {
        if (! notEmpty(value: $this->paymentToken))
        {
            throw new InvalidStripeTokenException;
        }
    }

    /**
     * @throws InvalidStripeChargeException
     */
    private function validateCharge(): void
    {
        if (! $this->charge instanceof Charge)
        {
            throw new InvalidStripeChargeException;
        }
    }

    /**
     * @throws StripePaymentFailedException
     */
    private function validateChargeStatus(): void
    {
        if (! $this->charge instanceof Charge || $this->charge->status !== 'succeeded')
        {
            throw new StripePaymentFailedException;
        }
    }
}
