<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\LetterCreativityEnum;

describe(description: 'LetterCreativityEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: LetterCreativityEnum::PRECISE->value)
            ->toBe(expected: 1)
            ->and(value: LetterCreativityEnum::BALANCED->value)
            ->toBe(expected: 2)
            ->and(value: LetterCreativityEnum::DYNAMIC->value)
            ->toBe(expected: 3)
            ->and(value: LetterCreativityEnum::CREATIVE->value)
            ->toBe(expected: 4);
    });

    it('getOptions returns all cases with labels and descriptions', function (): void
    {
        $options = LetterCreativityEnum::getOptions();

        expect(value: $options)
            ->toBeArray();

        foreach (LetterCreativityEnum::cases() as $case)
        {
            expect(value: $options)
                ->toHaveKey(key: $case->value)
                ->and(value: $options[$case->value]['label'])
                ->toBeString()
                ->and(value: $options[$case->value]['description'])
                ->toBeString();
        }
    });

    it('option returns correct structure for each case', function (): void
    {
        foreach (LetterCreativityEnum::cases() as $case)
        {
            $option = $case->option();

            expect(value: $option)
                ->toHaveKey(key: $case->value)
                ->and(value: $option[$case->value]['label'])
                ->toBeString()
                ->and(value: $option[$case->value]['description'])
                ->toBeString();
        }
    });

    it('label returns a string for each case', function (): void
    {
        foreach (LetterCreativityEnum::cases() as $case)
        {
            expect(value: $case->label())
                ->toBeString();
        }
    });

    it('description returns a string for each case', function (): void
    {
        foreach (LetterCreativityEnum::cases() as $case)
        {
            expect(value: $case->description())->toBeString();
        }
    });

    it('temperature returns the correct float for each case', function (): void
    {
        expect(value: LetterCreativityEnum::PRECISE->temperature())
            ->toBe(expected: 0.25)
            ->and(value: LetterCreativityEnum::BALANCED->temperature())
            ->toBe(expected: 0.5)
            ->and(value: LetterCreativityEnum::DYNAMIC->temperature())
            ->toBe(expected: 0.75)
            ->and(value: LetterCreativityEnum::CREATIVE->temperature())
            ->toBe(expected: 0.9);
    });
});
