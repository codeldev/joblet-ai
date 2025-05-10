<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PageSizeEnum;

it(description: 'has the correct number of variants', closure: function (): void
{
    expect(value: PageSizeEnum::cases())
        ->toHaveCount(count: 11);
});

it(description: 'assigns the correct values to cases', closure: function (): void
{
    expect(value: PageSizeEnum::LETTER->value)
        ->toBe(expected: 'letter')
        ->and(value: PageSizeEnum::LEGAL->value)
        ->toBe(expected: 'legal')
        ->and(value: PageSizeEnum::TABLOID->value)
        ->toBe(expected: 'tabloid')
        ->and(value: PageSizeEnum::LEDGER->value)
        ->toBe(expected: 'ledger')
        ->and(value: PageSizeEnum::A0->value)
        ->toBe(expected: 'a0')
        ->and(value: PageSizeEnum::A1->value)
        ->toBe(expected: 'a1')
        ->and(value: PageSizeEnum::A2->value)
        ->toBe(expected: 'a2')
        ->and(value: PageSizeEnum::A3->value)
        ->toBe(expected: 'a3')
        ->and(value: PageSizeEnum::A4->value)
        ->toBe(expected: 'a4')
        ->and(value: PageSizeEnum::A5->value)
        ->toBe(expected: 'a5')
        ->and(value: PageSizeEnum::A6->value)
        ->toBe(expected: 'a6');
});

it(description: 'returns the correct dimensions for each page size', closure: function (): void
{
    expect(value: PageSizeEnum::LETTER->getDimensions())
        ->toBe(expected: ['width' => 8.5, 'height' => 11.0])
        ->and(value: PageSizeEnum::LEGAL->getDimensions())
        ->toBe(expected: ['width' => 8.5, 'height' => 14.0])
        ->and(value: PageSizeEnum::TABLOID->getDimensions())
        ->toBe(expected: ['width' => 11.0, 'height' => 17.0])
        ->and(value: PageSizeEnum::LEDGER->getDimensions())
        ->toBe(expected: ['width' => 17.0, 'height' => 11.0])
        ->and(value: PageSizeEnum::A0->getDimensions())
        ->toBe(expected: ['width' => 33.1, 'height' => 46.8])
        ->and(value: PageSizeEnum::A1->getDimensions())
        ->toBe(expected: ['width' => 23.4, 'height' => 33.1])
        ->and(value: PageSizeEnum::A2->getDimensions())
        ->toBe(expected: ['width' => 16.54, 'height' => 23.4])
        ->and(value: PageSizeEnum::A3->getDimensions())
        ->toBe(expected: ['width' => 11.7, 'height' => 16.54])
        ->and(value: PageSizeEnum::A4->getDimensions())
        ->toBe(expected: ['width' => 8.27, 'height' => 11.7])
        ->and(value: PageSizeEnum::A5->getDimensions())
        ->toBe(expected: ['width' => 5.83, 'height' => 8.27])
        ->and(value: PageSizeEnum::A6->getDimensions())
        ->toBe(expected: ['width' => 4.13, 'height' => 5.83]);
});

it(description: 'generates correct size labels', closure: function (): void
{
    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: PageSizeEnum::class,
        method        : 'getSizeLabel'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $reflectionMethod->invoke(null, PageSizeEnum::LETTER))
        ->toBe(expected: 'Letter (8.5 x 11 inches)')
        ->and(value: $reflectionMethod->invoke(null, PageSizeEnum::A4))
        ->toBe(expected: 'A4 (8.27 x 11.7 inches)');
});

it(description: 'returns all page sizes as an associative array', closure: function (): void
{
    $pageSizes = PageSizeEnum::getPageSizes();

    expect(value: $pageSizes)
        ->toBeArray()
        ->toHaveCount(count: 11)
        ->toHaveKey(key: 'letter')
        ->toHaveKey(key: 'a4')
        ->and(value: $pageSizes['letter'])
        ->toBe(expected: 'Letter (8.5 x 11 inches)')
        ->and(value: $pageSizes['a4'])
        ->toBe(expected: 'A4 (8.27 x 11.7 inches)');
});

it(description: 'can be created from string values', closure: function (): void
{
    expect(value: PageSizeEnum::from(value: 'letter'))
        ->toBe(expected: PageSizeEnum::LETTER)
        ->and(value: PageSizeEnum::from(value: 'a4'))
        ->toBe(expected: PageSizeEnum::A4);
});

it(description: 'throws exception for invalid string values', closure: function (): void
{
    expect(value: fn () => PageSizeEnum::from(value: 'invalid-size'))
        ->toThrow(exception: ValueError::class);
});

it(description: 'returns array with correct type for getDimensions', closure: function (): void
{
    $dimensions = PageSizeEnum::A4->getDimensions();

    expect(value: $dimensions)
        ->toBeArray()
        ->toHaveCount(count: 2)
        ->toHaveKeys(keys: ['width', 'height'])
        ->and(value: $dimensions['width'])
        ->toBeFloat()
        ->toBe(expected: 8.27)
        ->and(value: $dimensions['height'])
        ->toBeFloat()
        ->toBe(expected: 11.7);
});

it(description: 'returns array with correct type for getPageSizes', closure: function (): void
{
    $pageSizes = PageSizeEnum::getPageSizes();

    expect(value: $pageSizes)
        ->toBeArray();

    foreach ($pageSizes as $key => $value)
    {
        expect(value: $key)
            ->toBeString()
            ->and(value: $value)
            ->toBeString()
            ->toContain(needles: 'inches');
    }
});
