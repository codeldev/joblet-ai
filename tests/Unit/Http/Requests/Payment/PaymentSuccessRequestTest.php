<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Payment\PaymentSuccessRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

it('sets session token and redirects when invoked', function (): void
{
    $request = new PaymentSuccessRequest;
    $token   = 'test-payment-token-' . uniqid(prefix: 'test-payment', more_entropy: true);

    $request->merge(input: ['token' => $token]);

    $response = $request->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-token'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-token'))
        ->toBe(expected: $token);
});

it('always returns true from authorize method', function (): void
{
    expect(value: (new PaymentSuccessRequest)->authorize())
        ->toBeTrue();
});

it('returns empty array from rules method', function (): void
{
    expect(value: (new PaymentSuccessRequest)->rules())
        ->toBe(expected: []);
});
