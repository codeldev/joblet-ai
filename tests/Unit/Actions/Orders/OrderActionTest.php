<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Orders\OrderAction;
use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Tests\Classes\Unit\Actions\Orders\TestInvalidPackageProcessor;
use Tests\Classes\Unit\Actions\Orders\TestOrderProcessor;
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

it('creates an order', function (): void
{
    $this->actingAs(user: $this->user);

    $testProcessor = new TestOrderProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $order = (new OrderAction)->handle(
        paymentData: $webhook->getPaymentData()
    );

    expect(value: $order)
        ->toBeInstanceOf(Order::class)
        ->and(value: $order->package_id)
        ->toBe(expected: $this->package->value)
        ->and(value: $order->package_name)
        ->toBe(expected: $this->package->title())
        ->and(value: $order->package_description)
        ->toBe(expected: $this->package->description())
        ->and(value: $order->price)
        ->toBe(expected: $this->package->price())
        ->and(value: $order->tokens)
        ->toBe(expected: $this->package->credits())
        ->and(value: $order->free)
        ->toBeFalse();
});

it('returns null if order creation fails due to invalid user', function (): void
{
    $testProcessor = new TestOrderProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook  = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $order = (new OrderAction)->handle(
        paymentData: $webhook->getPaymentData()
    );

    expect(value: $order)
        ->toBeNull();
});

it('returns null if order creation fails due to invalid package', function (): void
{
    $this->actingAs(user: $this->user);

    $testProcessor = new TestInvalidPackageProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook  = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $order = (new OrderAction)->handle(
        paymentData: $webhook->getPaymentData()
    );

    expect(value: $order)
        ->toBeNull();
});
