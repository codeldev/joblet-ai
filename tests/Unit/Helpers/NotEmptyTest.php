<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('returns true for non-empty strings', function (): void
{
    expect(value: notEmpty(value: 'test'))
        ->toBeTrue()
        ->and(value: notEmpty(value: '1'))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'a'))
        ->toBeTrue()
        ->and(value: notEmpty(value: '0.0'))
        ->toBeTrue()
        ->and(value: notEmpty(value: ' '))
        ->toBeTrue()
        ->and(value: notEmpty(value: '  '))
        ->toBeTrue()
        ->and(value: notEmpty(value: '0 '))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'false'))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'null'))
        ->toBeTrue();
});

it('returns false for empty strings', function (): void
{
    expect(value: notEmpty(value: ''))
        ->toBeFalse()
        ->and(value: notEmpty(value: '0'))
        ->toBeFalse()
        ->and(value: notEmpty(value: null))
        ->toBeFalse();
});

it('returns true for non-empty arrays', function (): void
{
    expect(value: notEmpty(value: [1, 2, 3]))
        ->toBeTrue()
        ->and(value: notEmpty(value: ['']))
        ->toBeTrue()
        ->and(value: notEmpty(value: [0]))
        ->toBeTrue()
        ->and(value: notEmpty(value: [null]))
        ->toBeTrue()
        ->and(value: notEmpty(value: [false]))
        ->toBeTrue()
        ->and(value: notEmpty(value: ['key' => 'value']))
        ->toBeTrue()
        ->and(value: notEmpty(value: [0 => 'value']))
        ->toBeTrue();
});

it('returns false for empty arrays', function (): void
{
    expect(value: notEmpty(value: []))
        ->toBeFalse();
});

it('handles boolean values correctly', function (): void
{
    expect(value: notEmpty(value: true))
        ->tobeTrue()
        ->and(value: notEmpty(value: false))
        ->tobeTrue();
});

it('handles numeric values correctly', function (): void
{
    expect(value: notEmpty(value: 0))
        ->toBeTrue()
        ->and(value: notEmpty(value: 1))
        ->toBeTrue()
        ->and(value: notEmpty(value: -1))
        ->toBeTrue()
        ->and(value: notEmpty(value: 0.0))
        ->toBeTrue()
        ->and(value: notEmpty(value: 1.1))
        ->toBeTrue();
});

it('handles objects correctly', function (): void
{
    expect(value: notEmpty(value: new stdClass()))
        ->toBeTrue()
        ->and(value: notEmpty(value: (object) ['property' => 'value']))
        ->toBeTrue();
});

it('handles objects with __toString method correctly', function (): void
{
    $objectWithToString = new class
    {
        public function __toString(): string
        {
            return 'test';
        }
    };

    $emptyObjectWithToString = new class
    {
        public function __toString(): string
        {
            return '';
        }
    };

    $zeroObjectWithToString = new class
    {
        public function __toString(): string
        {
            return '0';
        }
    };

    expect(value: notEmpty(value: $objectWithToString))
        ->toBeTrue()
        ->and(value: notEmpty(value: $emptyObjectWithToString))
        ->toBeFalse()
        ->and(value: notEmpty(value: $zeroObjectWithToString))
        ->toBeFalse();
});

it('handles callables correctly', function (): void
{
    expect(value: notEmpty(value: static fn () => true))
        ->toBeTrue()
        ->and(value: notEmpty(value: static fn () => true))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'strlen'))
        ->toBeTrue();
});

it('handles edge cases correctly', function (): void
{
    expect(value: notEmpty(value: ' '))
        ->toBeTrue()
        ->and(value: notEmpty(value: ' 0 '))
        ->toBeTrue()
        ->and(value: notEmpty(value: [null, '', '0']))
        ->toBeTrue()
        ->and(value: notEmpty(value: [[]]))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'false'))
        ->toBeTrue()
        ->and(value: notEmpty(value: 'true'))
        ->toBeTrue();
});
