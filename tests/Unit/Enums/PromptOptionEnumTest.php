<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PromptOptionEnum;

describe(description: 'PromptOptionEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: PromptOptionEnum::PROBLEM_SOLVING->value)
            ->toBe(expected: 1)
            ->and(value: PromptOptionEnum::GROWTH_INTEREST->value)
            ->toBe(expected: 2)
            ->and(value: PromptOptionEnum::UNIQUE_VALUE->value)
            ->toBe(expected: 3)
            ->and(value: PromptOptionEnum::ACHIEVEMENTS->value)
            ->toBe(expected: 4)
            ->and(value: PromptOptionEnum::MOTIVATION->value)
            ->toBe(expected: 5)
            ->and(value: PromptOptionEnum::AMBITIONS->value)
            ->toBe(expected: 6)
            ->and(value: PromptOptionEnum::OTHER->value)
            ->toBe(expected: 7);
    });

    it('text returns a system prompt string for each case', function (): void
    {
        collect(value: PromptOptionEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->systemPrompt())->toBeString());
    });

    it('text returns a user prompt string for each case', function (): void
    {
        collect(value: PromptOptionEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->userPrompt(fake()->sentence()))->toBeString());
    });
});
