<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Orders\OrderAction;
use App\Actions\Orders\PaymentAction;
use App\Contracts\Http\Requests\Webhooks\WebhookRequestInterface;
use App\Contracts\Services\PaymentGateways\PaymentProcessorInterface;
use App\Http\Requests\Webhooks\StripeWebhookRequest;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestErrorProcessor;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestOrderActionHandler;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestPaymentActionHandler;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestSuccessProcessAction;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestWebhookPaymentReturnEmpty;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestWebhookPaymentReturnNull;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestWebhookPaymentWithEmptyData;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestWebhookPaymentWithError;
use Tests\Classes\Unit\Http\Requests\Webhooks\TestWebhookPaymentWithValidData;

beforeEach(closure: function (): void
{
    app()->forgetInstance(PaymentProcessorInterface::class);
});

test(description: 'StripeWebhookRequest implements WebhookRequestInterface', closure: function (): void
{
    expect(value: class_implements(object_or_class: StripeWebhookRequest::class))
        ->toHaveKey(key: WebhookRequestInterface::class);
});

test(description: 'StripeWebhookRequest extends FormRequest', closure: function (): void
{
    expect(value: get_parent_class(object_or_class: StripeWebhookRequest::class))
        ->toBe(expected: FormRequest::class);
});

test(description: 'StripeWebhookRequest is marked as final', closure: function (): void
{
    expect(value: new ReflectionClass(objectOrClass: StripeWebhookRequest::class)->isFinal())
        ->toBeTrue();
});

test(description: 'rules method returns empty array', closure: function (): void
{
    expect(value: (new StripeWebhookRequest)->rules())
        ->toBe(expected: []);
});

test(description: 'authorize returns false when stripe-signature header is missing', closure: function (): void
{
    config()->set(key: 'cashier.webhook.secret', value: 'test-secret');

    expect(value: makeStripeRequest()->authorize())
        ->toBeFalse();
});

test(description: 'authorize returns false when webhook secret is not a string', closure: function (): void
{
    config()->set(key: 'cashier.webhook.secret');

    $request = makeStripeRequest(
        headers: ['stripe-signature' => 'abc'],
        content: '{"foo": "bar"}'
    );

    expect(value: $request->authorize())
        ->toBeFalse();
});

test(description: 'authorize returns true for valid Stripe signature', closure: function (): void
{
    $secret    = 'whsec_testsecret';
    $payload   = '{"foo":"bar"}';
    $timestamp = (string) time();
    $signature = hash_hmac(algo: 'sha256', data: $timestamp . '.' . $payload, key: $secret);

    config()->set('cashier.webhook.secret', $secret);

    $request = makeStripeRequest(
        headers: ['stripe-signature' => "t={$timestamp},v1={$signature}"],
        content: $payload
    );

    expect(value: $request->authorize())
        ->toBeTrue();
});

test(description: 'invoke processes real Stripe payment data', closure: function (): void
{
    $payload  = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/stripe_payment_intent_succeeded.json')
    );

    expect(value: makeStripeRequest(content: $payload)->__invoke())
        ->toBeInstanceOf(class: JsonResponse::class);
});

test(description: 'authorize returns false when signature verification throws an exception', closure: function (): void
{
    config()->set('cashier.webhook.secret', 'secret');

    $request = makeStripeRequest(
        headers: ['stripe-signature' => 't=1,v1=invalid'],
        content: '{"foo": "bar"}'
    );

    expect(value: $request->authorize())
        ->toBeFalse();
});

test(description: '__invoke returns error response when payment data is null', closure: function (): void
{
    app()->instance(
        abstract: PaymentWebhook::class,
        instance: new TestWebhookPaymentReturnNull
    );

    $response = makeStripeRequest(content: '{"foo": "bar"}')->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true)['error'])
        ->toBe(expected: 'Invalid charge data');
});

test(description: '__invoke returns error response when payment data is empty array', closure: function (): void
{
    app()->instance(
        abstract: PaymentWebhook::class,
        instance: new TestWebhookPaymentReturnEmpty
    );

    $response = makeStripeRequest(content: '{"foo": "bar"}')->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true)['error'])
        ->toBe(expected: 'Invalid charge data');
});

test(description: 'returns error response when PaymentWebhook receives invalid payload', closure: function (): void
{
    $response = makeStripeRequest(content: '{invalid json')->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(true)['error'])
        ->not()->toBe(expected: '');
});

test(description: 'returns error response when PaymentProcessor has error', closure: function (): void
{
    app()->bind(
        abstract: PaymentProcessorInterface::class,
        concrete: fn () => new TestErrorProcessor()
    );

    $payload = json_encode(value: [
        'type' => 'checkout.session.completed',
        'data' => ['object' => ['id' => 'cs_test_123']],
    ], flags: JSON_THROW_ON_ERROR);

    $response = makeStripeRequest(content: $payload)->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true)['error'])
        ->toBe(expected: 'Invalid charge data');
});

test(description: '__invoke processes payment data for supported event', closure: function (): void
{
    app()->forgetInstance(
        abstract: PaymentProcessorInterface::class
    );

    app()->bind(abstract: OrderAction::class, concrete: fn () => new TestOrderActionHandler());

    app()->bind(abstract: PaymentAction::class, concrete: fn () => new TestPaymentActionHandler());

    $payload = json_encode(value: [
        'type' => 'checkout.session.completed',
        'data' => ['object' => ['id' => 'cs_test_123']],
    ], flags: JSON_THROW_ON_ERROR);

    expect(value: makeStripeRequest(content: $payload)->__invoke())
        ->toBeInstanceOf(class: JsonResponse::class);
});

test(description: '__invoke processes payment data and covers line 25', closure: function (): void
{
    $payload  = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/stripe_payment_intent_succeeded.json')
    );

    expect(value: makeStripeRequest(content: $payload)->__invoke())
        ->toBeInstanceOf(class: JsonResponse::class);
});

test(description: '__invoke returns error response when PaymentWebhook has error', closure: function (): void
{
    $mockPaymentWebhook = new TestWebhookPaymentWithError();

    $request = makeStripeRequest();

    $request->setPaymentWebhook(
        paymentWebhook: $mockPaymentWebhook
    );

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true))
        ->toHaveKey(key: 'error')
        ->and(value: $response->getData(assoc: true)['error'])
        ->toBe(expected: 'Test error message');
});

test(description: '__invoke returns error for empty payment data', closure: function (): void
{
    $mockPaymentWebhook = new TestWebhookPaymentWithEmptyData();

    $request = makeStripeRequest();

    $request->setPaymentWebhook(
        paymentWebhook: $mockPaymentWebhook
    );

    $response = $request->__invoke();

    expect(value: $response)->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true))->toHaveKey(key: 'error')
        ->and(value: $response->getData(assoc: true)['error'])->toBe(expected: trans('exception.stripe.invalid.charge'));
});

test(description: '__invoke calls ProcessAction with valid payment data', closure: function (): void
{
    $mockPaymentWebhook = new TestWebhookPaymentWithValidData();

    $mockProcessAction = new TestSuccessProcessAction();

    $request = makeStripeRequest();

    $request->setPaymentWebhook(
        paymentWebhook: $mockPaymentWebhook
    );

    $request->setProcessAction(
        processAction: $mockProcessAction
    );

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: JsonResponse::class)
        ->and(value: $response->getData(assoc: true))
        ->toHaveKey(key: 'success')
        ->and(value: $response->getData(assoc: true)['success'])
        ->toBeTrue();
});
