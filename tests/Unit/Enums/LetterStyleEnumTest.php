<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\LetterStyleEnum;

describe(description: 'LetterStyleEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: LetterStyleEnum::CASUAL->value)
            ->toBe(expected: 1)
            ->and(value: LetterStyleEnum::FORMAL->value)
            ->toBe(expected: 2)
            ->and(value: LetterStyleEnum::PRO->value)
            ->toBe(expected: 3);
    });

    it('text returns a string for each case', function (): void
    {
        collect(value: LetterStyleEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->text())->toBeString());
    });
});
