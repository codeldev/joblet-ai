<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test(description: 'payment table has expected fields', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new Payment)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'order_id',
        'user_id',
        'invoice_number',
        'amount',
        'gateway',
        'card_type',
        'card_last4',
        'event_id',
        'intent_id',
        'charge_id',
        'transaction_id',
        'receipt_url',
        'payment_token',
        'created_at',
        'updated_at',
    ]);
});

test(description: 'payment has correct casts', closure: function (): void
{
    expect(value: (new Payment)->getCasts())->toMatchArray(array: [
        'gateway'    => 'encrypted',
        'card_type'  => 'encrypted',
        'card_last4' => 'encrypted',
        'amount'     => 'integer',
    ]);
});

test(description: 'payment belongs to order', closure: function (): void
{
    expect(value: (new Payment)->order()->getRelated())
        ->toBeInstanceOf(class: Order::class);
});

test(description: 'payment belongs to user', closure: function (): void
{
    expect(value: (new Payment)->user()->getRelated())
        ->toBeInstanceOf(class: User::class);
});
