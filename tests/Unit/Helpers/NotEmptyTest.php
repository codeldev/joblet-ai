<?php

declare(strict_types=1);

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('returns true for non-empty strings', function (): void
{
    assertTrue(notEmpty(value: 'test'));
    assertTrue(notEmpty(value: '1'));
    assertTrue(notEmpty(value: 'a'));
    assertTrue(notEmpty(value: '0.0'));
    assertTrue(notEmpty(value: ' '));
    assertTrue(notEmpty(value: '  '));
    assertTrue(notEmpty(value: '0 '));
    assertTrue(notEmpty(value: 'false'));
    assertTrue(notEmpty(value: 'null'));
});

it('returns false for empty strings', function (): void
{
    assertFalse(notEmpty(value: ''));
    assertFalse(notEmpty(value: '0'));
    assertFalse(notEmpty(value: null));
});

it('returns true for non-empty arrays', function (): void
{
    assertTrue(notEmpty(value: [1, 2, 3]));
    assertTrue(notEmpty(value: ['']));
    assertTrue(notEmpty(value: [0]));
    assertTrue(notEmpty(value: [null]));
    assertTrue(notEmpty(value: [false]));
    assertTrue(notEmpty(value: ['key' => 'value']));
    assertTrue(notEmpty(value: [0 => 'value']));
});

it('returns false for empty arrays', function (): void
{
    assertFalse(notEmpty(value: []));
});

it('handles boolean values correctly', function (): void
{
    assertTrue(notEmpty(value: true));
    assertTrue(notEmpty(value: false));
});

it('handles numeric values correctly', function (): void
{
    assertTrue(notEmpty(value: 0));
    assertTrue(notEmpty(value: 1));
    assertTrue(notEmpty(value: -1));
    assertTrue(notEmpty(value: 0.0));
    assertTrue(notEmpty(value: 1.1));
});

it('handles objects correctly', function (): void
{
    assertTrue(notEmpty(value: new stdClass()));
    assertTrue(notEmpty(value: (object) ['property' => 'value']));
});

it('handles resources correctly', function (): void
{
    $resource = fopen('php://memory', 'r');
    assertTrue(notEmpty(value: $resource));
    fclose($resource);
});

it('handles callables correctly', function (): void
{
    assertTrue(notEmpty(value: fn () => true));

    assertTrue(notEmpty(value: fn () => true));

    assertTrue(notEmpty(value: 'strlen'));
});

it('handles edge cases correctly', function (): void
{
    // Empty string with spaces
    assertTrue(notEmpty(value: ' '));

    // String zero with spaces
    assertTrue(notEmpty(value: ' 0 '));

    // Array with empty values
    assertTrue(notEmpty(value: [null, '', '0']));

    // Nested empty array
    assertTrue(notEmpty(value: [[]]));

    // String that looks like boolean
    assertTrue(notEmpty(value: 'false'));
    assertTrue(notEmpty(value: 'true'));
});
