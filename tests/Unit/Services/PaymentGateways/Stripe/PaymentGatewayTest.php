<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use App\Services\PaymentGateways\Stripe\PaymentGateway;

beforeEach(function (): void
{
    $this->currency = 'gbp';
    $this->user     = testUser();
    $this->package  = ProductPackageEnum::PACKAGE_A;
    $this->gateway  = new PaymentGateway(
        package: $this->package,
        user   : $this->user
    );
});

describe(description: 'PaymentGateway', tests: function (): void
{
    it('returns the correct payment processor name', function (): void
    {
        expect(value: $this->gateway->paymentProcessor())
            ->toBe(expected: 'stripe');
    });

    it('generates a payment token with the correct format', function (): void
    {
        expect(value: $this->gateway->generatePaymentToken())
            ->toContain(expected: (string) $this->user->id)
            ->toContain(expected: (string) $this->package->value);
    });

    it('builds data with all required fields', function (): void
    {
        $token  = $this->gateway->generatePaymentToken();
        $result = $this->gateway->buildData();

        expect(value: $result)
            ->toBeArray()
            ->toHaveKeys(keys: [
                'line_items',
                'metadata',
                'client_reference_id',
                'customer_email',
                'mode',
                'currency',
                'cancel_url',
                'success_url',
            ])
            ->and(value: $result['client_reference_id'])
            ->toBe(expected: $this->user->id)
            ->and(value: $result['customer_email'])
            ->toBe(expected: $this->user->email)
            ->and(value: $result['mode'])
            ->toBe(expected: 'payment')
            ->and(value: $result['currency'])
            ->toBe(expected: $this->currency)
            ->and(value: $result['metadata'])
            ->toBeArray()
            ->toHaveKeys(keys: ['package', 'user', 'token'])
            ->and(value: $result['metadata']['package'])
            ->toBe(expected: $this->package->value)
            ->and(value: $result['metadata']['user'])
            ->toBe(expected: $this->user->id)
            ->and(value: $result['metadata']['token'])
            ->toBe(expected: $token)
            ->and(value: $result['line_items'])
            ->toBeArray()
            ->and(value: $result['line_items'][0])
            ->toBeArray()
            ->toHaveKeys(keys: ['price', 'quantity'])
            ->and(value: $result['line_items'][0]['price'])
            ->toBe(expected: $this->package->stripeId())
            ->and(value: $result['line_items'][0]['quantity'])
            ->toBe(expected: 1)
            ->and(value: $result['success_url'])
            ->toContain(route(name: 'payment.success', parameters: [
                'gateway' => 'stripe',
                'token'   => $token,
            ]))
            ->and(value: $result['cancel_url'])
            ->toContain(route(name: 'payment.cancel', parameters: 'stripe'));
    });

    it('returns a valid stripe payment url', function (): void
    {
        // Create a mock session object with a URL property
        $mockSession      = new stdClass();
        $mockSession->url = 'https://checkout.stripe.com/test-session';

        // Mock the Session::create static method
        Mockery::mock('alias:Stripe\\Checkout\\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn($mockSession);

        expect(value: $this->gateway->process())
            ->toBeString()
            ->toEqual('https://checkout.stripe.com/test-session');
    });

    it('returns null when process encounters a runtime exception', function (): void
    {
        config(key: ['cashier.secret' => null]);

        expect(value: $this->gateway->process())
            ->toBeNull();
    });

    it('throws exception when API key is invalid', function (): void
    {
        config(key: ['cashier.secret' => null]);

        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'getApiKey'
        );

        $method->setAccessible(accessible: true);

        expect(value: fn () => $method->invoke(object: $this->gateway))
            ->toThrow(exception: RuntimeException::class, message: 'Invalid Stripe API key');
    });

    it('returns valid API key', function (): void
    {
        $secret = str()->random(64);

        config(key: ['cashier.secret' => $secret]);

        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'getApiKey'
        );

        $method->setAccessible(accessible: true);

        expect(value: $method->invoke(object: $this->gateway))
            ->toBe(expected: $secret);
    });

    it('builds line items with correct structure', function (): void
    {
        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'buildLineItems'
        );

        $method->setAccessible(accessible: true);

        $result = $method->invoke(object: $this->gateway);

        expect(value: $result)
            ->toBeArray()
            ->and(value: $result[0])
            ->toBeArray()
            ->toHaveKeys(keys: ['price', 'quantity'])
            ->and(value: $result[0]['price'])
            ->toBe(expected: $this->package->stripeId())
            ->and(value: $result[0]['quantity'])
            ->toBe(expected: 1);
    });

    it('builds metadata with correct structure', function (): void
    {
        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'buildMetaData'
        );

        $method->setAccessible(accessible: true);

        $testToken = Str::uuid()->toString();
        $result    = $method->invoke($this->gateway, $testToken);

        expect(value: $result)
            ->toBeArray()
            ->toHaveKeys(keys: ['package', 'user', 'token'])
            ->and(value: $result['package'])
            ->toBe(expected: $this->package->value)
            ->and(value: $result['user'])
            ->toBe(expected: $this->user->id)
            ->and(value: $result['token'])
            ->toBe(expected: $testToken);
    });

    it('builds success URL with correct parameters', function (): void
    {
        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'successUrl'
        );

        $method->setAccessible(accessible: true);

        $testToken  = Str::uuid()->toString();
        $successUrl = route(name: 'payment.success', parameters: [
            'gateway' => 'stripe',
            'token'   => $testToken,
        ]);

        expect(value: $method->invoke($this->gateway, $testToken))
            ->toBe(expected: $successUrl);
    });

    it('builds cancel URL with correct parameters', function (): void
    {
        $method = new ReflectionMethod(
            objectOrMethod: PaymentGateway::class,
            method        : 'cancelUrl'
        );

        $method->setAccessible(accessible: true);

        $cancelUrl = route(
            name      : 'payment.cancel',
            parameters: 'stripe'
        );

        expect(value: $method->invoke(object: $this->gateway))
            ->toBe(expected: $cancelUrl);
    });
});
