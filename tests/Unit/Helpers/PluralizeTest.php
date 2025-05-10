<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

test(description: 'pluralize handles zero amount', closure: function (): void
{
    expect(value: pluralize(amount: 0, word: 'item'))
        ->toBe(expected: '0 items');
});

test(description: 'pluralize handles single amount', closure: function (): void
{
    expect(value: pluralize(amount: 1, word: 'item'))
        ->toBe(expected: '1 item');
});

test(description: 'pluralize handles multiple amount', closure: function (): void
{
    expect(value: pluralize(amount: 2, word: 'item'))
        ->toBe(expected: '2 items');
});

test(description: 'pluralize handles decimal amount', closure: function (): void
{
    expect(value: pluralize(amount: 2.5, word: 'item'))
        ->toBe(expected: '2.5 items');
});

test(description: 'pluralize handles title case', closure: function (): void
{
    expect(value: pluralize(amount: 2, word: 'item', titleCase: true))
        ->toBe(expected: '2 Items');
});
