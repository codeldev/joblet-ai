<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;
use App\Enums\ProductPackageEnum;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestErrorWebhookProcessor;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestServiceProvider;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestWebhookProcessor;

beforeEach(closure: function (): void
{
    $this->app->register(provider: TestServiceProvider::class);

    $this->package = ProductPackageEnum::PACKAGE_A;
    $this->user    = testUser();

    $validPayload = [
        'id'   => 'evt_' . uniqid(prefix: 'webhook-event', more_entropy: true),
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
                'payment_intent'      => 'pi_' . uniqid(prefix: 'webhook-intent', more_entropy: true),
                'metadata'            => [
                    'package' => $this->package->value,
                    'token'   => "token_{$this->user->id}_{$this->package->value}",
                ],
            ],
        ],
    ];

    $invalidPayload = [
        'id'   => 'evt_' . uniqid(prefix: 'webhook-event', more_entropy: true),
        'type' => 'payment_intent.created',
        'data' => [
            'object' => [
                'client_reference_id' => $this->user->id,
            ],
        ],
    ];

    $this->invalidJsonPayload = '{invalid_json:';

    $this->validPayload = json_encode(
        value: $validPayload,
        flags: JSON_THROW_ON_ERROR
    );

    $this->invalidTypePayload = json_encode(
        value: $invalidPayload,
        flags: JSON_THROW_ON_ERROR
    );

    $this->emptyPayload = json_encode(
        value: [],
        flags: JSON_THROW_ON_ERROR
    );

    $this->nonArrayPayload = json_encode(
        value: 'not_an_array',
        flags: JSON_THROW_ON_ERROR
    );
});

describe(description: 'PaymentWebhook', tests: function (): void
{
    it('returns payment data when payload is valid', function (): void
    {
        $payload = [
            'id'   => 'evt_' . uniqid(prefix: 'webhook-event', more_entropy: true),
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'client_reference_id' => $this->user->id,
                    'payment_intent'      => 'pi_' . uniqid(prefix: 'webhook-intent', more_entropy: true),
                    'metadata'            => [
                        'package' => $this->package->value,
                        'token'   => "token_{$this->user->id}_{$this->package->value}",
                    ],
                ],
            ],
        ];

        $testProcessor = new TestWebhookProcessor();

        $this->app->bind(
            abstract: PaymentProcessorInterface::class,
            concrete: fn () => $testProcessor
        );

        $webhook = new PaymentWebhook(stripePayload: json_encode(
            value: $payload,
            flags: JSON_THROW_ON_ERROR
        ));

        $result = $webhook->getPaymentData();

        expect(value: $result)
            ->toBeArray()
            ->toHaveKey(key: 'gateway')
            ->and(value: $result['gateway'])
            ->toBe(expected: 'stripe')
            ->and(value: $webhook->getError())
            ->toBeNull();
    });

    it('returns null and sets error when payload has invalid type', function (): void
    {
        $webhook = new PaymentWebhook(
            stripePayload: $this->invalidTypePayload
        );

        expect(value: $webhook->getPaymentData())
            ->toBeNull()
            ->and(value: $webhook->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when payload is empty', function (): void
    {
        $webhook = new PaymentWebhook(
            stripePayload: $this->emptyPayload
        );

        expect(value: $webhook->getPaymentData())
            ->toBeNull()
            ->and(value: $webhook->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when payload is invalid JSON', function (): void
    {
        $webhook = new PaymentWebhook(
            stripePayload: $this->invalidJsonPayload
        );

        expect(value: $webhook->getPaymentData())
            ->toBeNull()
            ->and(value: $webhook->getError())
            ->not->toBeNull();
    });

    it('returns null when payload is not an array after decoding', function (): void
    {
        $webhook = new PaymentWebhook(
            stripePayload: $this->nonArrayPayload
        );

        expect(value: $webhook->getPaymentData())
            ->toBeNull()
            ->and(value: $webhook->getError())
            ->not->toBeNull();
    });

    it('returns null and sets error when PaymentProcessor returns error', function (): void
    {
        $payload = [
            'id'   => 'evt_' . uniqid(prefix: 'webhook-event', more_entropy: true),
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'client_reference_id' => $this->user->id,
                ],
            ],
        ];

        $testProcessor = new TestErrorWebhookProcessor();

        $this->app->bind(
            abstract: PaymentProcessorInterface::class,
            concrete: fn () => $testProcessor
        );

        $webhook = new PaymentWebhook(stripePayload: json_encode(
            value: $payload,
            flags: JSON_THROW_ON_ERROR
        ));

        expect(value: $webhook->getPaymentData())
            ->toBeNull()
            ->and(value: $webhook->getError())
            ->toBe(expected: 'Payment processor error');
    });

    it('tests PaymentWebhook implements PaymentWebhookInterface', function (): void
    {
        expect(value: new PaymentWebhook(stripePayload: $this->validPayload))
            ->toBeInstanceOf(class: PaymentWebhookInterface::class);
    });
});
