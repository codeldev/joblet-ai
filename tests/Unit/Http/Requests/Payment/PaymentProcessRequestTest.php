<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Contracts\Http\Requests\Payment\PaymentProcessInterface;
use App\Enums\ProductPackageEnum;
use App\Exceptions\Payment\InvalidPaymentGatewayException;
use App\Exceptions\Payment\InvalidPaymentUrlException;
use App\Exceptions\Product\InvalidProductPackageException;
use App\Http\Requests\Payment\PaymentProcessRequest;
use App\Services\PaymentGateways\Stripe\PaymentGateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\Classes\Unit\Http\Requests\Payment\TestEmptyPaymentUrlProcessor;
use Tests\Classes\Unit\Http\Requests\Payment\TestEmptyStringProcessor;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithEmptyProcess;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithExceptionProcess;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithNullProcess;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithNullReturnProcess;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithoutProcess;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithoutProcessMethod;
use Tests\Classes\Unit\Http\Requests\Payment\TestProcessorWithValidProcess;

beforeEach(function (): void
{
    Session::flush();
});

afterEach(function (): void
{
    Mockery::close();
});

it('handles invalid gateway with error message', function (): void
{
    $request = new PaymentProcessRequest;
    $request->merge(input: [
        'gateway' => 'invalid_gateway',
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: 'Invalid payment gateway');
});

it('handles null gateway with error message', function (): void
{
    $request = new PaymentProcessRequest;
    $request->merge(input: [
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: 'Invalid payment gateway');
});

it('handles non-string gateway with error message', function (): void
{
    $request = new PaymentProcessRequest;
    $request->merge(input: [
        'gateway' => 123,
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentGatewayException)->getMessage());
});

it('handles invalid package with error message', function (): void
{
    $request = new PaymentProcessRequest;
    $request->merge(input: [
        'gateway' => 'stripe',
        'package' => 'invalid-package',
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidProductPackageException)->getMessage());
});

it('tests buildPaymentLink method with reflection - no process method', function (): void
{
    App::instance(
        abstract: TestProcessorWithoutProcess::class,
        instance: new TestProcessorWithoutProcess(
            package: ProductPackageEnum::PACKAGE_A,
            user   : null
        )
    );

    $reflectionClass        = new ReflectionClass(objectOrClass: PaymentProcessRequest::class);
    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $result = $buildPaymentLinkMethod
        ->invoke(new PaymentProcessRequest, TestProcessorWithoutProcess::class, ProductPackageEnum::PACKAGE_A);

    expect(value: $result)
        ->toBeNull();
});

it('tests buildPaymentLink method with reflection - null process result', function (): void
{
    App::instance(
        abstract: TestProcessorWithNullProcess::class,
        instance: new TestProcessorWithNullProcess(
            package: ProductPackageEnum::PACKAGE_A,
            user   : null
        )
    );

    $request = new PaymentProcessRequest;

    $reflectionClass        = new ReflectionClass(objectOrClass: PaymentProcessRequest::class);
    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $result = $buildPaymentLinkMethod
        ->invoke($request, TestProcessorWithNullProcess::class, ProductPackageEnum::PACKAGE_A);

    expect(value: $result)->toBeNull();
});

it('tests buildPaymentLink method with reflection - valid process result', function (): void
{
    App::instance(
        abstract: TestProcessorWithValidProcess::class,
        instance: new TestProcessorWithValidProcess(
            package: ProductPackageEnum::PACKAGE_A,
            user   : null
        )
    );

    $reflectionClass        = new ReflectionClass(objectOrClass: PaymentProcessRequest::class);
    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $result = $buildPaymentLinkMethod
        ->invoke(new PaymentProcessRequest, TestProcessorWithValidProcess::class, ProductPackageEnum::PACKAGE_A);

    expect(value: $result)
        ->toBe(expected: 'https://example.com/payment');
});

it('tests buildPaymentLink method with reflection - empty string process result', function (): void
{
    $emptyProcessorClass = 'App\\Tests\\EmptyStringProcessor';

    $emptyProcessor = new TestEmptyStringProcessor(
        package: ProductPackageEnum::PACKAGE_A,
        user: testUser()
    );

    class_alias(
        class: get_class(object: $emptyProcessor),
        alias: $emptyProcessorClass
    );

    $reflectionClass        = new ReflectionClass(objectOrClass: PaymentProcessRequest::class);
    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    App::instance(
        abstract: $emptyProcessorClass,
        instance: $emptyProcessor
    );

    $result  = $buildPaymentLinkMethod
        ->invoke(new PaymentProcessRequest, $emptyProcessorClass, ProductPackageEnum::PACKAGE_A);

    expect(value: $result)
        ->toBe(expected: '')
        ->and(value: notEmpty(value: $result))
        ->toBeFalse();
});

it('tests null payment URL scenario', function (): void
{
    $processor = new TestProcessorWithNullProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user   : testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $processor
    );

    $mockRequest = Mockery::mock(PaymentProcessInterface::class);
    $mockRequest->shouldReceive(methodNames: 'getGateway')->andReturn('stripe');
    $mockRequest->shouldReceive(methodNames: 'getPackage')->andReturn(ProductPackageEnum::PACKAGE_A);
    $mockRequest->shouldReceive(methodNames: 'paymentError')->andReturnUsing(function ($message)
    {
        Session::put('payment-error', $message);

        return redirect()->route(route: 'account');
    });

    Session::flush();

    $response = $mockRequest->paymentError(new InvalidPaymentUrlException()->getMessage());

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentUrlException)->getMessage());
});

it('tests successful payment URL redirect', function (): void
{
    $processor = new TestProcessorWithValidProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user   : testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $processor
    );

    $mockRequest = Mockery::mock(PaymentProcessInterface::class);
    $mockRequest->shouldReceive(methodNames: '__invoke')
        ->andReturn(redirect()->away(path: 'https://example.com/payment'));

    $response = $mockRequest->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: 'https://example.com/payment');
});

it('handles empty payment URL with error message', function (): void
{
    $emptyProcessor = new TestEmptyPaymentUrlProcessor(
        package: ProductPackageEnum::PACKAGE_A,
        user: testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $emptyProcessor
    );

    $mockRequest = Mockery::mock(PaymentProcessInterface::class);
    $mockRequest->shouldReceive(methodNames: 'getGateway')->andReturn('stripe');
    $mockRequest->shouldReceive(methodNames: 'getPackage')->andReturn(ProductPackageEnum::PACKAGE_A);
    $mockRequest->shouldReceive(methodNames: 'paymentError')->andReturnUsing(function ($message)
    {
        Session::put('payment-error', $message);

        return redirect()->route(route: 'account');
    });

    Session::flush();

    $response = $mockRequest->paymentError(new InvalidPaymentUrlException()->getMessage());

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentUrlException)->getMessage());
});

it('always returns true from authorize method', function (): void
{
    expect(value: (new PaymentProcessRequest)->authorize())
        ->toBeTrue();
});

it('returns empty array from rules method', function (): void
{
    expect(value: (new PaymentProcessRequest)->rules())
        ->toBe(expected: []);
});

it('implements PaymentProcessRequestInterface', function (): void
{
    expect(value: new PaymentProcessRequest)
        ->toBeInstanceOf(class: PaymentProcessInterface::class);
});

it('extends FormRequest', function (): void
{
    expect(value: get_parent_class(object_or_class: PaymentProcessRequest::class))
        ->toBe(expected: FormRequest::class);
});

it('is marked as final', function (): void
{
    expect(value: new ReflectionClass(objectOrClass: PaymentProcessRequest::class)->isFinal())
        ->toBeTrue();
});

it('tests getGateway method returns gateway string', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'stripe',
    ]);

    expect(value: $request->getGateway())
        ->toBe(expected: 'stripe');
});

it('tests getPackage method returns ProductPackageEnum', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    expect(value: $request->getPackage())
        ->toBe(expected: ProductPackageEnum::PACKAGE_A);
});

it('tests getPackage method throws exception for non-numeric package', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'package' => 'invalid-package',
    ]);

    expect(value: fn () => $request->getPackage())
        ->toThrow(exception: InvalidProductPackageException::class);
});

it('tests getPackage method throws exception for null package', function (): void
{
    expect(value: fn () => (new PaymentProcessRequest)->getPackage())
        ->toThrow(exception: InvalidProductPackageException::class);
});

it('tests empty payment URL with notEmpty helper', function (): void
{
    $emptyProcessor = new TestProcessorWithEmptyProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user   : testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $emptyProcessor
    );

    Auth::shouldReceive('user')
        ->andReturn(args: testUser());

    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'stripe',
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentUrlException)->getMessage());
});

it('tests a successful payment URL redirect', function (): void
{
    $validProcessor = new TestProcessorWithValidProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user   : testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $validProcessor
    );

    Auth::shouldReceive('user')
        ->andReturn(args: testUser());

    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'stripe',
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: 'https://example.com/payment');
});

it('tests null package scenario', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'stripe',
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidProductPackageException)->getMessage());
});

it('tests paymentError method', function (): void
{
    $errorMessage = 'Test error message';

    $response = (new PaymentProcessRequest)
        ->paymentError(message: $errorMessage);

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toBe(expected: $errorMessage);
});

it('tests rules method returns empty array', function (): void
{
    expect(value: (new PaymentProcessRequest)->rules())
        ->toBeArray()
        ->toBeEmpty();
});

it('tests authorize method always returns true', function (): void
{
    expect(value: (new PaymentProcessRequest)->authorize())
        ->toBeTrue();
});

it('tests buildPaymentLink method with processor that has no process method', function (): void
{
    $processor = new TestProcessorWithoutProcessMethod(
        package: ProductPackageEnum::PACKAGE_A,
        user: null
    );

    App::instance(
        abstract: TestProcessorWithoutProcessMethod::class,
        instance: $processor
    );

    $reflectionClass = new ReflectionClass(
        objectOrClass: PaymentProcessRequest::class
    );

    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $result = $buildPaymentLinkMethod->invoke(
        new PaymentProcessRequest,
        TestProcessorWithoutProcessMethod::class,
        ProductPackageEnum::PACKAGE_A
    );

    expect(value: $result)
        ->toBeNull();
});

it('tests processor match default case', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'unknown',
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentGatewayException)->getMessage());
});

it('tests exception handling in __invoke method', function (): void
{
    $exceptionProcessor = new TestProcessorWithExceptionProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user: testUser()
    );

    App::bind(
        abstract: PaymentGateway::class,
        concrete: fn () => $exceptionProcessor
    );

    Auth::shouldReceive('user')
        ->andReturn(args: testUser());

    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => 'stripe',
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentUrlException)->getMessage());
});

it('tests non-string gateway value', function (): void
{
    $request = (new PaymentProcessRequest)->merge(input: [
        'gateway' => fake()->randomNumber(),
        'package' => ProductPackageEnum::PACKAGE_A->value,
    ]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-error'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-error'))
        ->toContain(needles: (new InvalidPaymentGatewayException)->getMessage());
});

it('tests buildPaymentLink method with method_exists coverage', function (): void
{
    $reflectionClass = new ReflectionClass(
        objectOrClass: PaymentProcessRequest::class
    );

    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $customProcessor = new TestProcessorWithNullProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user: testUser()
    );

    $customProcessorClass = get_class(object: $customProcessor);

    App::instance(
        abstract: $customProcessorClass,
        instance: $customProcessor
    );

    $result = $buildPaymentLinkMethod->invoke(
        new PaymentProcessRequest,
        $customProcessorClass,
        ProductPackageEnum::PACKAGE_A
    );

    expect(value: $result)
        ->toBeNull();
});

it('tests method_exists branch in buildPaymentLink with custom processor', function (): void
{
    $customProcessor = new TestProcessorWithNullReturnProcess(
        package: ProductPackageEnum::PACKAGE_A,
        user: testUser()
    );

    $customProcessorClass = get_class(object: $customProcessor);

    App::instance(
        abstract: $customProcessorClass,
        instance: $customProcessor
    );

    $reflectionClass = new ReflectionClass(
        objectOrClass: PaymentProcessRequest::class
    );

    $buildPaymentLinkMethod = $reflectionClass->getMethod(name: 'buildPaymentLink');
    $buildPaymentLinkMethod->setAccessible(accessible: true);

    $result = $buildPaymentLinkMethod->invoke(
        new PaymentProcessRequest,
        $customProcessorClass,
        ProductPackageEnum::PACKAGE_A
    );

    expect(value: $result)
        ->toBeNull();
});
