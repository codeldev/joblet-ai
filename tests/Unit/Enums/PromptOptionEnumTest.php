<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PromptOptionEnum;

describe(description: 'PromptOptionEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: PromptOptionEnum::TRANSITION->value)
            ->toBe(expected: 1)
            ->and(value: PromptOptionEnum::REASON->value)
            ->toBe(expected: 2)
            ->and(value: PromptOptionEnum::EXPERIENCE->value)
            ->toBe(expected: 3)
            ->and(value: PromptOptionEnum::GRATITUDE->value)
            ->toBe(expected: 4);
    });

    it('text returns a string for each case', function (): void
    {
        collect(value: PromptOptionEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->text())->toBeString());
    });
});
