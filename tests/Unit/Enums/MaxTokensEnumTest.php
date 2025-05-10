<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\MaxTokensEnum;

it('has the correct number of variants', function (): void
{
    expect(value: MaxTokensEnum::cases())
        ->toHaveCount(count: 3);
});

it('assigns the correct values to cases', function (): void
{
    expect(value: MaxTokensEnum::SHORT->value)
        ->toBe(expected: 1)
        ->and(value: MaxTokensEnum::MEDIUM->value)
        ->toBe(expected: 2)
        ->and(value: MaxTokensEnum::LONG->value)
        ->toBe(expected: 3);
});

it('returns the correct token count for each variant', function (): void
{
    expect(value: MaxTokensEnum::SHORT->tokens())
        ->toBe(expected: 1250)
        ->and(value: MaxTokensEnum::MEDIUM->tokens())
        ->toBe(expected: 1850)
        ->and(value: MaxTokensEnum::LONG->tokens())
        ->toBe(expected: 2500);
});

it('has increasing token values as enum value increases', function (): void
{
    $previousTokens = 0;

    foreach (MaxTokensEnum::cases() as $case)
    {
        expect(value: $case->tokens())
            ->toBeGreaterThan(expected: $previousTokens);

        $previousTokens = $case->tokens();
    }
});

it('can be instantiated from value', function (): void
{
    expect(value: MaxTokensEnum::from(value: 1))
        ->toBe(expected: MaxTokensEnum::SHORT)
        ->and(value: MaxTokensEnum::from(value: 2))
        ->toBe(expected: MaxTokensEnum::MEDIUM)
        ->and(value: MaxTokensEnum::from(value: 3))
        ->toBe(expected: MaxTokensEnum::LONG);
});

it('throws exception when instantiated with invalid value', function (): void
{
    MaxTokensEnum::from(value: 4);
})->throws(exception: ValueError::class);

it('can try to get enum case from value', function (): void
{
    expect(value: MaxTokensEnum::tryFrom(value: 2))
        ->toBe(expected: MaxTokensEnum::MEDIUM)
        ->and(value: MaxTokensEnum::tryFrom(value: 10))
        ->toBeNull();
});

it('ensures token values are meaningful and within expected ranges', function (): void
{
    foreach (MaxTokensEnum::cases() as $case)
    {
        expect(value: $case->tokens())
            ->toBeGreaterThan(expected: 0)
            ->and(value: $case->tokens())
            ->toBeLessThan(expected: 10000);
    }
});
