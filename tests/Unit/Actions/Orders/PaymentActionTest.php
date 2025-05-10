<?php

/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Orders\OrderAction;
use App\Actions\Orders\PaymentAction;
use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Tests\Classes\Unit\Actions\Orders\TestPaymentProcessor;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestServiceProvider;

beforeEach(closure: function (): void
{
    $this->app->register(provider: TestServiceProvider::class);

    $this->package = ProductPackageEnum::STANDARD;
    $this->user    = testUserWithoutEvents();

    $this->payloadData = [
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
        value: $this->payloadData,
        flags: JSON_THROW_ON_ERROR
    );
});

it('creates a payment for an order', function (): void
{
    $this->actingAs(user: $this->user);

    $testProcessor = new TestPaymentProcessor();

    $this->app->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => $testProcessor
    );

    $webhook = new PaymentWebhook(
        stripePayload: $this->payload
    );

    $paymentData = $webhook->getPaymentData();

    $order = (new OrderAction)->handle(
        paymentData: $paymentData,
    );

    $response = (new PaymentAction)->handle(
        order      : $order,
        paymentData: $paymentData
    );

    $result = json_decode(
        json       : $response->getContent(),
        associative: false,
        depth      : 256,
        flags      : JSON_THROW_ON_ERROR
    );

    $payment = Payment::first();

    expect(value: $result)
        ->toHaveKey(key: 'success')
        ->and(value: $result->success)
        ->toBe(expected: trans(key: 'payment.webhook.success.order.payment'))
        ->and(value: Order::count())
        ->toBe(expected: 1)
        ->and(value: Payment::count())
        ->toBe(expected: 1)
        ->and(value: $payment->order_id)
        ->toBe(expected: $order->id)
        ->and(value: $payment->user_id)
        ->toBe(expected: $this->user->id)
        ->and(value: $payment->amount)
        ->toBe(expected: $this->package->price())
        ->and(value: $payment->gateway)
        ->toBe(expected: 'stripe');
});
