<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Generated;
use App\Models\Usage;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test(description: 'usage table has expected fields', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new Usage)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'user_id',
        'generated_id',
        'word_count',
        'tokens_used',
        'created_at',
        'updated_at',
    ]);
});

test(description: 'usage has correct casts', closure: function (): void
{
    expect(value: (new Usage)->getCasts())->toMatchArray(array: [
        'word_count'  => 'integer',
        'tokens_used' => 'integer',
    ]);
});

test(description: 'usage belongs to user', closure: function (): void
{
    expect(value: (new Usage)->user()->getRelated())
        ->toBeInstanceOf(class: User::class);
});

test(description: 'usage belongs to asset (generated)', closure: function (): void
{
    expect(value: (new Usage)->asset()->getRelated())
        ->toBeInstanceOf(class: Generated::class);
});
