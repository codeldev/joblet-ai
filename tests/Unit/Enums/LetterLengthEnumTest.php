<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\LetterLengthEnum;

describe(description: 'LetterLengthEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: LetterLengthEnum::SHORT->value)
            ->toBe(expected: 1)
            ->and(value: LetterLengthEnum::MEDIUM->value)
            ->toBe(expected: 2)
            ->and(value: LetterLengthEnum::LONG->value)
            ->toBe(expected: 3);
    });

    it('getOptions returns all cases with labels and descriptions', function (): void
    {
        $options = LetterLengthEnum::getOptions();

        expect(value: $options)
            ->toBeArray();

        collect(value: LetterLengthEnum::cases())->each(callback: function ($case) use ($options): void
        {
            expect(value: $options)
                ->toHaveKey(key: $case->value)
                ->and(value: $options[$case->value]['label'])
                ->toBeString()
                ->and(value: $options[$case->value]['description'])
                ->toBeString();
        });
    });

    it('option returns correct structure for each case', function (): void
    {
        collect(value: LetterLengthEnum::cases())->each(callback: function ($case): void
        {
            $option = $case->option();

            expect(value: $option)
                ->toHaveKey(key: $case->value)
                ->and(value: $option[$case->value]['label'])
                ->toBeString()
                ->and(value: $option[$case->value]['description'])
                ->toBeString();
        });
    });

    it('label returns a string for each case', function (): void
    {
        collect(value: LetterLengthEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->label())->toBeString());
    });

    it('description returns a string for each case', function (): void
    {
        collect(value: LetterLengthEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->description())->toBeString());
    });

    it('text returns a string for each case', function (): void
    {
        collect(value: LetterLengthEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->text())->toBeString());
    });
});
