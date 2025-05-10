<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\PaymentGateways\Stripe\StripeServiceInterface;
use App\Services\PaymentGateways\Stripe\StripeService;
use Stripe\Charge;
use Tests\Classes\Unit\Services\PaymentGateways\Stripe\TestCharge;
use Tests\Classes\Vendor\Stripe\MockEmptyStripeClient;
use Tests\Classes\Vendor\Stripe\MockStripeClientWithCharge;
use Tests\Classes\Vendor\Stripe\MockStripeClientWithChargeException;
use Tests\Classes\Vendor\Stripe\MockStripeClientWithException;
use Tests\Classes\Vendor\Stripe\MockStripeClientWithNonStringCharge;

beforeEach(closure: function (): void
{
    $this->paymentIntentId = 'pi_' . uniqid(prefix: 'test', more_entropy: true);
    $this->chargeId        = 'ch_' . uniqid(prefix: 'test', more_entropy: true);
});

test(description: 'it implements StripeServiceInterface', closure: function (): void
{
    expect(value: new StripeService)
        ->toBeInstanceOf(class: StripeServiceInterface::class);
});

test(description: 'it throws exception when stripe secret is not a string', closure: function (): void
{
    config()->set(key: 'cashier.secret');

    expect(value: fn () => new StripeService)->toThrow(
        exception       : RuntimeException::class,
        exceptionMessage: 'Stripe secret must be a string or a client must be provided'
    );
});

test(description: 'it has correct method signature for getChargeFromPaymentIntent', closure: function (): void
{
    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: StripeService::class,
        method: 'getChargeFromPaymentIntent'
    );

    $parameters = $reflectionMethod->getParameters();

    expect(value: $reflectionMethod->getReturnType()->getName())
        ->toBe(expected: Charge::class)
        ->and(value: $reflectionMethod->getReturnType()->allowsNull())
        ->toBeTrue()
        ->and(value: count(value: $parameters))
        ->toBe(expected: 1)
        ->and(value: $parameters[0]->getName())
        ->toBe(expected: 'paymentIntentId')
        ->and(value: $parameters[0]->getType()->getName())
        ->toBe(expected: 'string');
});

test(description: 'returns charge when payment intent has a charge', closure: function (): void
{
    config()->set(key: 'cashier.secret', value: 'sk_test_valid_key');

    $charge     = TestCharge::createSuccessfulCharge();
    $charge->id = $this->chargeId;

    $paymentIntent                = new stdClass();
    $paymentIntent->latest_charge = $this->chargeId;

    $mockClient = new MockStripeClientWithCharge(
        paymentIntent  : $paymentIntent,
        charge         : $charge,
        paymentIntentId: $this->paymentIntentId,
        chargeId       : $this->chargeId
    );

    $stripeService = new StripeService(client: $mockClient);
    $chargeResult  = $stripeService->getChargeFromPaymentIntent(paymentIntentId: $this->paymentIntentId);

    expect(value: $chargeResult)
        ->toBeInstanceOf(class: Charge::class)
        ->and(value: $chargeResult->id)
        ->toBe(expected: $this->chargeId);
});

test(description: 'returns null when payment intent latest_charge is not a string', closure: function (): void
{
    config()->set(key: 'cashier.secret', value: 'sk_test_valid_key');

    $paymentIntent                = new stdClass();
    $paymentIntent->latest_charge = 123;

    $mockClient = new MockStripeClientWithNonStringCharge(
        paymentIntent  : $paymentIntent,
        paymentIntentId: $this->paymentIntentId
    );

    $stripeService = new StripeService(client: $mockClient);
    $chargeResult  = $stripeService->getChargeFromPaymentIntent(paymentIntentId: $this->paymentIntentId);

    expect(value: $chargeResult)->toBeNull();
});

test(description: 'returns null when exception is thrown', closure: function (): void
{
    config()->set(key: 'cashier.secret', value: 'sk_test_valid_key');

    $mockClient = new MockStripeClientWithException(
        paymentIntentId : $this->paymentIntentId,
        exceptionMessage: 'Stripe API error'
    );

    $stripeService = new StripeService(client: $mockClient);
    $chargeResult  = $stripeService->getChargeFromPaymentIntent(paymentIntentId: $this->paymentIntentId);

    expect(value: $chargeResult)->toBeNull();
});

test(description: 'returns null when charge retrieval throws exception', closure: function (): void
{
    config()->set(key: 'cashier.secret', value: 'sk_test_valid_key');

    $paymentIntent                = new stdClass();
    $paymentIntent->latest_charge = $this->chargeId;

    $mockClient = new MockStripeClientWithChargeException(
        paymentIntent   : $paymentIntent,
        paymentIntentId : $this->paymentIntentId,
        chargeId        : $this->chargeId,
        exceptionMessage: 'Failed to retrieve charge'
    );

    $stripeService = new StripeService(client: $mockClient);
    $chargeResult  = $stripeService->getChargeFromPaymentIntent(paymentIntentId: $this->paymentIntentId);

    expect(value: $chargeResult)
        ->toBeNull();
});

test(description: 'handles invalid config with a mock client', closure: function (): void
{
    config()->set(key: 'cashier.secret');

    $stripeService = new StripeService(client: new MockEmptyStripeClient);

    expect(value: $stripeService->getChargeFromPaymentIntent(paymentIntentId: 'invalid_id'))
        ->toBeNull();
});
