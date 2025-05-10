<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;
use App\Exceptions\Stripe\InvalidStripeEventException;
use App\Exceptions\Stripe\InvalidStripeUserException;
use App\Services\PaymentGateways\Stripe\PaymentProcessor;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestCharge;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestStripeService;

beforeEach(function (): void
{
    $this->user          = testUser();
    $this->package       = ProductPackageEnum::PACKAGE_A;
    $this->paymentToken  = "token_{$this->user->id}_{$this->package->value}";
    $this->eventId       = 'evt_' . uniqid(prefix: 'test', more_entropy: true);
    $this->paymentIntent = 'pi_' . uniqid(prefix: 'test', more_entropy: true);

    $this->successfulCharge         = TestCharge::createSuccessfulCharge();
    $this->failedCharge             = TestCharge::createFailedCharge();
    $this->chargeWithoutCardDetails = TestCharge::createChargeWithoutCardDetails();

    $this->successfulCharge->payment_intent         = $this->paymentIntent;
    $this->failedCharge->payment_intent             = $this->paymentIntent;
    $this->chargeWithoutCardDetails->payment_intent = $this->paymentIntent;

    $this->validPayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'package' => $this->package->value,
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidEventPayload = [
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'package' => $this->package->value,
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidUserPayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'payment_intent' => $this->paymentIntent,
                'metadata'       => [
                    'package' => $this->package->value,
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidUserIdPayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => 'non-existent-user-id',
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'package' => $this->package->value,
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidIntentPayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'metadata'            => [
                    'package' => $this->package->value,
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidPackagePayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'token' => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidPackageValuePayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'package' => 'not-a-number',
                    'token'   => $this->paymentToken,
                ],
            ],
        ],
    ];

    $this->invalidTokenPayload = [
        'id'   => $this->eventId,
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => $this->paymentIntent,
                'metadata'            => [
                    'package' => $this->package->value,
                ],
            ],
        ],
    ];
});

describe(description: 'PaymentProcessor', tests: function (): void
{
    it('implements PaymentProcessorInterface', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor)
            ->toBeInstanceOf(class: PaymentProcessorInterface::class);
    });

    it('returns null and sets error when event ID is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidEventPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull()
            ->and(value: $processor->getError())
            ->toBe(expected: new InvalidStripeEventException()->getMessage());
    });

    it('returns null and sets error when user is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidUserPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull()
            ->and(value: $processor->getError())
            ->toBe(expected: new InvalidStripeUserException()->getMessage());
    });

    it('returns null and sets error when user ID is invalid', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidUserIdPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull()
            ->and(value: $processor->getError())
            ->toBe(expected: new InvalidStripeUserException()->getMessage());
    });

    it('returns null and sets error when payment intent is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidIntentPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when package is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidPackagePayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when package value is invalid', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidPackageValuePayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when payment token is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->invalidTokenPayload,
            stripeService: new TestStripeService
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when charge is missing', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService(charge: null)
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when charge status is not succeeded', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService(charge: $this->failedCharge)
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });

    it('returns payment data with null card details when card info is missing', function (): void
    {
        $charge         = $this->chargeWithoutCardDetails;
        $charge->status = 'succeeded';

        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService(charge: $charge)
        );

        $result = $processor->getChargeData();

        expect(value: $result)
            ->toBeArray()
            ->toHaveKeys(keys: [
                'gateway',
                'user',
                'package',
                'amount',
                'card_type',
                'card_last4',
                'event_id',
                'intent_id',
                'charge_id',
                'transaction_id',
                'receipt_url',
                'payment_token',
            ])
            ->and(value: $result['card_type'])
            ->toBeNull()
            ->and(value: $result['card_last4'])
            ->toBeNull();
    });

    it('returns complete payment data when all conditions are met', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService(charge: $this->successfulCharge)
        );

        $result = $processor->getChargeData();

        expect(value: $result)
            ->toBeArray()
            ->toHaveKeys(keys: [
                'gateway',
                'user',
                'package',
                'amount',
                'card_type',
                'card_last4',
                'event_id',
                'intent_id',
                'charge_id',
                'transaction_id',
                'receipt_url',
                'payment_token',
            ])
            ->and(value: $result['gateway'])
            ->toBe(expected: 'stripe')
            ->and(value: $result['user']->id)
            ->toBe(expected: $this->user->id)
            ->and(value: $result['package'])
            ->toBe(expected: $this->package)
            ->and(value: $result['amount'])
            ->toBe(expected: $this->successfulCharge->amount)
            ->and(value: $result['card_type'])
            ->toBe(expected: 'visa')
            ->and(value: $result['card_last4'])
            ->toBe(expected: '4242')
            ->and(value: $result['event_id'])
            ->toBe(expected: $this->eventId)
            ->and(value: $result['intent_id'])
            ->toBe(expected: $this->successfulCharge->payment_intent)
            ->and(value: $result['charge_id'])
            ->toBe(expected: $this->successfulCharge->id)
            ->and(value: $result['transaction_id'])
            ->toBe(expected: $this->successfulCharge->balance_transaction)
            ->and(value: $result['receipt_url'])
            ->toBe(expected: $this->successfulCharge->receipt_url)
            ->and(value: $result['payment_token'])
            ->toBe(expected: $this->paymentToken);
    });

    it('handles exceptions when retrieving payment intent', function (): void
    {
        $processor = new PaymentProcessor(
            payload      : $this->validPayload,
            stripeService: new TestStripeService(throwException: true)
        );

        expect(value: $processor->getChargeData())
            ->toBeNull()
            ->and(value: $processor->getError())
            ->not->toBeNull();
    });
});
