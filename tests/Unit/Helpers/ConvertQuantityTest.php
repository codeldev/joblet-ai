<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

test(description: 'convertQuantity handles whole numbers', closure: function (): void
{
    expect(value: convertQuantity(quantity: 5.00))
        ->toBe(expected: '5');
});

test(description: 'convertQuantity handles decimals', closure: function (): void
{
    expect(value: convertQuantity(quantity: 5.50))
        ->toBe(expected: '5.5');
});

test(description: 'convertQuantity handles long decimals', closure: function (): void
{
    expect(value: convertQuantity(quantity: 5.55))
        ->toBe(expected: '5.55');
});

test(description: 'convertQuantity handles trailing zeros', closure: function (): void
{
    expect(value: convertQuantity(quantity: 5.500))
        ->toBe(expected: '5.5');
});
