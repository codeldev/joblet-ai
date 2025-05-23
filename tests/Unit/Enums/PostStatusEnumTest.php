<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PostStatusEnum;

describe(description: 'PostStatusEnum', tests: function (): void
{
    it('has the correct cases defined', function (): void
    {
        expect(value: PostStatusEnum::cases())
            ->toHaveCount(count: 5)
            ->and(value: PostStatusEnum::SCHEDULED->value)
            ->toBe(expected: 1)
            ->and(value: PostStatusEnum::PUBLISHED->value)
            ->toBe(expected: 2)
            ->and(value: PostStatusEnum::DRAFT->value)
            ->toBe(expected: 3)
            ->and(value: PostStatusEnum::ARCHIVED->value)
            ->toBe(expected: 4)
            ->and(value: PostStatusEnum::PENDING_IMAGE->value)
            ->toBe(expected: 5);
    });

    it('can be instantiated from value', function (): void
    {
        expect(value: PostStatusEnum::from(value: 1))
            ->toBe(expected: PostStatusEnum::SCHEDULED)
            ->and(value: PostStatusEnum::from(value: 2))
            ->toBe(expected: PostStatusEnum::PUBLISHED)
            ->and(value: PostStatusEnum::from(value: 3))
            ->toBe(expected: PostStatusEnum::DRAFT)
            ->and(value: PostStatusEnum::from(value: 4))
            ->toBe(expected: PostStatusEnum::ARCHIVED)
            ->and(value: PostStatusEnum::from(value: 5))
            ->toBe(expected: PostStatusEnum::PENDING_IMAGE);
    });

    it('throws exception for invalid value', function (): void
    {
        expect(value: fn () => PostStatusEnum::from(value: 999))
            ->toThrow(exception: ValueError::class);
    });

    it('can be used in type-hint and comparison', function (): void
    {
        $isDraft = fn (PostStatusEnum $status): bool => $status === PostStatusEnum::DRAFT;

        expect(value: $isDraft(status: PostStatusEnum::DRAFT))
            ->toBeTrue()
            ->and(value: $isDraft(status: PostStatusEnum::PUBLISHED))
            ->toBeFalse();
    });
});
