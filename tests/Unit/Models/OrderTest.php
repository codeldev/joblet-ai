<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test(description: 'order table has expected fields', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new Order)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'user_id',
        'package_id',
        'package_name',
        'package_description',
        'price',
        'tokens',
        'free',
        'created_at',
        'updated_at',
    ]);
});

test(description: 'order has correct casts', closure: function (): void
{
    expect(value: (new Order)->getCasts())->toMatchArray(array: [
        'price'      => 'integer',
        'tokens'     => 'integer',
        'free'       => 'boolean',
        'package_id' => 'integer',
    ]);
});

test(description: 'order belongs to user', closure: function (): void
{
    expect(value: (new Order)->user()->getRelated())
        ->toBeInstanceOf(class: User::class);
});

test(description: 'order has one payment', closure: function (): void
{
    expect(value: (new Order)->payment()->getRelated())
        ->toBeInstanceOf(class: Payment::class);
});
