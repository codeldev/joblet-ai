<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\BlogImageTypeEnum;

describe(description: 'BlogImageTypeEnum', tests: function (): void
{
    it('has the correct cases defined', function (): void
    {
        expect(value: BlogImageTypeEnum::cases())
            ->toHaveCount(count: 2)
            ->and(value: BlogImageTypeEnum::FEATURED->value)
            ->toBe(expected: 1)
            ->and(value: BlogImageTypeEnum::CONTENT->value)
            ->toBe(expected: 2);
    });

    it('can be instantiated from value', function (): void
    {
        expect(value: BlogImageTypeEnum::from(value: 1))
            ->toBe(expected: BlogImageTypeEnum::FEATURED)
            ->and(value: BlogImageTypeEnum::from(value: 2))
            ->toBe(expected: BlogImageTypeEnum::CONTENT);
    });

    it('throws exception for invalid value', function (): void
    {
        expect(value: fn () => BlogImageTypeEnum::from(value: 999))
            ->toThrow(exception: ValueError::class);
    });

    it('can be used in type-hint and comparison', function (): void
    {
        $testWithEnum = fn (BlogImageTypeEnum $type): bool => $type === BlogImageTypeEnum::FEATURED;

        expect(value: $testWithEnum(type: BlogImageTypeEnum::FEATURED))
            ->toBeTrue()
            ->and(value: $testWithEnum(type: BlogImageTypeEnum::CONTENT))
            ->toBeFalse();
    });
});
