<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use Carbon\Carbon;

it('has the correct number of variants', function (): void
{
    expect(value: DateFormatEnum::cases())
        ->toHaveCount(count: 9);
});

it('assigns the correct values to cases', function (): void
{
    expect(value: DateFormatEnum::VARIANT_A->value)
        ->toBe(expected: 1)
        ->and(value: DateFormatEnum::VARIANT_B->value)
        ->toBe(expected: 2)
        ->and(value: DateFormatEnum::VARIANT_C->value)
        ->toBe(expected: 3)
        ->and(value: DateFormatEnum::VARIANT_D->value)
        ->toBe(expected: 4)
        ->and(value: DateFormatEnum::VARIANT_E->value)
        ->toBe(expected: 5)
        ->and(value: DateFormatEnum::VARIANT_F->value)
        ->toBe(expected: 6)
        ->and(value: DateFormatEnum::VARIANT_G->value)
        ->toBe(expected: 7)
        ->and(value: DateFormatEnum::VARIANT_H->value)
        ->toBe(expected: 8)
        ->and(value: DateFormatEnum::VARIANT_I->value)
        ->toBe(expected: 9);
});

it('returns the correct format string for each variant', function (): void
{
    expect(value: DateFormatEnum::VARIANT_A->format())
        ->toBe(expected: 'd/m/Y')
        ->and(value: DateFormatEnum::VARIANT_B->format())
        ->toBe(expected: 'm/d/Y')
        ->and(value: DateFormatEnum::VARIANT_C->format())
        ->toBe(expected: 'Y-m-d')
        ->and(value: DateFormatEnum::VARIANT_D->format())
        ->toBe(expected: 'Y/m/d')
        ->and(value: DateFormatEnum::VARIANT_E->format())
        ->toBe(expected: 'M/d/Y')
        ->and(value: DateFormatEnum::VARIANT_F->format())
        ->toBe(expected: 'jS F Y')
        ->and(value: DateFormatEnum::VARIANT_G->format())
        ->toBe(expected: 'F jS, Y')
        ->and(value: DateFormatEnum::VARIANT_H->format())
        ->toBe(expected: 'jS M Y')
        ->and(value: DateFormatEnum::VARIANT_I->format())
        ->toBe(expected: 'M jS, Y');
});

it('generates correct formatted date for each variant', function (): void
{
    Carbon::setTestNow(
        testNow: Carbon::create(year: 2025, month: 2, day: 15, hour: 12)
    );

    expect(value: DateFormatEnum::VARIANT_A->item())
        ->toBe(expected: [1 => '15/02/2025'])
        ->and(value: DateFormatEnum::VARIANT_B->item())
        ->toBe(expected: [2 => '02/15/2025'])
        ->and(value: DateFormatEnum::VARIANT_C->item())
        ->toBe(expected: [3 => '2025-02-15'])
        ->and(value: DateFormatEnum::VARIANT_D->item())
        ->toBe(expected: [4 => '2025/02/15'])
        ->and(value: DateFormatEnum::VARIANT_E->item())
        ->toBe(expected: [5 => 'Feb/15/2025'])
        ->and(value: DateFormatEnum::VARIANT_F->item())
        ->toBe(expected: [6 => '15th February 2025'])
        ->and(value: DateFormatEnum::VARIANT_G->item())
        ->toBe(expected: [7 => 'February 15th, 2025'])
        ->and(value: DateFormatEnum::VARIANT_H->item())
        ->toBe(expected: [8 => '15th Feb 2025'])
        ->and(value: DateFormatEnum::VARIANT_I->item())
        ->toBe(expected: [9 => 'Feb 15th, 2025']);
});

it('returns all formats in getFormats method', function (): void
{
    Carbon::setTestNow(
        testNow: Carbon::create(year: 2025, month: 2, day: 15, hour: 12)
    );

    $formats = DateFormatEnum::getFormats();

    expect(value: $formats)
        ->toBeArray()
        ->and(value: $formats)
        ->toHaveCount(9)
        ->and(value: $formats[1])
        ->toBe(expected: '15/02/2025')
        ->and(value: $formats[2])
        ->toBe(expected: '02/15/2025')
        ->and(value: $formats[3])
        ->toBe(expected: '2025-02-15')
        ->and(value: $formats[4])
        ->toBe(expected: '2025/02/15')
        ->and(value: $formats[5])
        ->toBe(expected: 'Feb/15/2025')
        ->and(value: $formats[6])
        ->toBe(expected: '15th February 2025')
        ->and(value: $formats[7])
        ->toBe(expected: 'February 15th, 2025')
        ->and(value: $formats[8])
        ->toBe(expected: '15th Feb 2025')
        ->and(value: $formats[9])
        ->toBe(expected: 'Feb 15th, 2025');
});

it('ensures each format produces a unique output', function (): void
{
    Carbon::setTestNow(
        testNow: Carbon::create(year: 2025, month: 2, day: 15, hour: 12)
    );

    $formats = DateFormatEnum::getFormats();
    $unique  = array_unique(array: $formats);

    expect(value: count(value: $unique))
        ->toBe(expected: count(value: $formats));
});

it('can be instantiated from value', function (): void
{
    expect(value: DateFormatEnum::from(value: 1))
        ->toBe(expected: DateFormatEnum::VARIANT_A)
        ->and(value: DateFormatEnum::from(value: 5))
        ->toBe(expected: DateFormatEnum::VARIANT_E)
        ->and(value: DateFormatEnum::from(value: 9))
        ->toBe(expected: DateFormatEnum::VARIANT_I);
});

it('throws exception when instantiated with invalid value', function (): void
{
    DateFormatEnum::from(value: 10);
})->throws(exception: ValueError::class);

it('can try to get enum case from value', function (): void
{
    expect(value: DateFormatEnum::tryFrom(value: 3))
        ->toBe(expected: DateFormatEnum::VARIANT_C)
        ->and(value: DateFormatEnum::tryFrom(value: 100))
        ->toBeNull();
});
