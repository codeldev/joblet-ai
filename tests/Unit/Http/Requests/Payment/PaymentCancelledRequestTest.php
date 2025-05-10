<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Payment\PaymentCancelledRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

it('sets session and redirects when invoked', function (): void
{
    $response = (new PaymentCancelledRequest)->__invoke();

    expect(value: $response)
        ->toBeInstanceOf(class: RedirectResponse::class)
        ->and(value: $response->getTargetUrl())
        ->toBe(expected: route(name: 'account'))
        ->and(value: Session::has(key: 'payment-cancelled'))
        ->toBeTrue()
        ->and(value: Session::get(key: 'payment-cancelled'))
        ->toBeTrue();
});

it('always returns true from authorize method', function (): void
{
    expect(value: (new PaymentCancelledRequest)->authorize())
        ->toBeTrue();
});

it('returns empty array from rules method', function (): void
{
    expect(value: (new PaymentCancelledRequest)->rules())
        ->toBe(expected: []);
});
