<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Orders\ProcessAction;
use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Tests\Classes\Unit\Actions\Orders\TestProcessProcessor;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestServiceProvider;

beforeEach(closure: function (): void
{
    $this->app->register(provider: TestServiceProvider::class);

    $this->package = ProductPackageEnum::STANDARD;
    $this->user    = testUserWithoutEvents();

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

    $this->payload = json_encode(
        value: $payload,
        flags: JSON_THROW_ON_ERROR
    );
});

it('creates an order with a payment', function (): void
{
    $this->actingAs(user: $this->user);

    $testProcessor = new TestProcessProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $response = (new ProcessAction)->handle(
        paymentData: $webhook->getPaymentData()
    );

    $result = json_decode(
        json       : $response->getContent(),
        associative: false,
        depth      : 256,
        flags      : JSON_THROW_ON_ERROR
    );

    expect(value: $result)
        ->toHaveKey(key: 'success')
        ->and(value: $result->success)
        ->toBe(expected: trans(key: 'payment.webhook.success.order.payment'))
        ->and(value: Order::count())
        ->toBe(expected: 1)
        ->and(value: Payment::count())
        ->toBe(expected: 1);
});

it('returns an error if order creation fails', function (): void
{
    $testProcessor = new TestProcessProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $response = (new ProcessAction)->handle(
        paymentData: $webhook->getPaymentData()
    );

    $result = json_decode(
        json       : $response->getContent(),
        associative: false,
        depth      : 256,
        flags      : JSON_THROW_ON_ERROR
    );

    expect(value: $result)
        ->toHaveKey(key: 'error')
        ->and(value: $result->error)
        ->toBe(expected: trans(key: 'payment.webhook.error.order.failed'))
        ->and(value: Order::count())
        ->toBe(expected: 0)
        ->and(value: Payment::count())
        ->toBe(expected: 0);
});
