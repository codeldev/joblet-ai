<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Accounting;
use Illuminate\Support\Facades\Schema;

beforeEach(closure: function (): void
{
    $this->accounting = Accounting::factory()->make(attributes: [
        'price'          => 1000,
        'amount'         => 2000,
        'card_type'      => 'visa',
        'card_last4'     => '4242',
        'invoice_number' => 1234,
    ]);
});

test(description: 'accounting table has expected fields', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new Accounting)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'user_name',
        'user_email',
        'order_id',
        'package_id',
        'package_name',
        'package_description',
        'price',
        'tokens',
        'payment_id',
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

test(description: 'accounting has correct casts', closure: function (): void
{
    expect(value: (new Accounting)->getCasts())->toMatchArray(array: [
        'user_name'  => 'encrypted',
        'user_email' => 'encrypted',
        'price'      => 'integer',
        'tokens'     => 'integer',
        'package_id' => 'integer',
        'gateway'    => 'encrypted',
        'card_type'  => 'encrypted',
        'card_last4' => 'encrypted',
        'amount'     => 'integer',
    ]);
});

test(description: 'formatted_amount returns correctly formatted amount', closure: function (): void
{
    $accounting = Accounting::factory()->make(attributes: [
        'amount' => 2000,
    ]);

    expect(value: $accounting->formatted_amount)
        ->toBeString()
        ->toContain(needles: '20');
});

test(description: 'formatted_price returns correctly formatted price', closure: function (): void
{
    $accounting = Accounting::factory()->make(attributes: [
        'price' => 1000,
    ]);

    expect(value: $accounting->formatted_price)
        ->toBeString()
        ->toContain(needles: '10');
});

test(description: 'payment_method returns correctly formatted payment method', closure: function (): void
{
    $accounting = Accounting::factory()->make(attributes: [
        'card_type'  => 'visa',
        'card_last4' => '4242',
    ]);

    expect(value: $accounting->payment_method)
        ->toBeString()
        ->toContain(needles: 'visa')
        ->toContain(needles: '4242');
});

test(description: 'formatted_invoice_number returns padded invoice number', closure: function (): void
{
    // Set a known padding value in the config
    config()->set(key: 'settings.invoices.padding', value: 6);

    $accounting = Accounting::factory()->make(attributes: [
        'invoice_number' => 1234,
    ]);

    expect(value: $accounting->formatted_invoice_number)
        ->toBe(expected: '001234');
});

test(description: 'formatted_invoice_number handles different padding values', closure: function (): void
{
    // Test with a different padding value
    config()->set(key: 'settings.invoices.padding', value: 8);

    $accounting = Accounting::factory()->make(attributes: [
        'invoice_number' => 1234,
    ]);

    expect(value: $accounting->formatted_invoice_number)
        ->toBe(expected: '00001234');
});
