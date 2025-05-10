<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\PromptOptionEnum;
use App\Services\Generator\UserPrompt;

use function Pest\Laravel\actingAs;

beforeEach(closure: function (): void
{
    actingAs(user: testUser());

    $this->userPromptName           = fake()->name();
    $this->userPromptJobTitle       = fake()->jobTitle();
    $this->userPromptJobDescription = fake()->paragraphs(nb: 5, asText: true);
    $this->userPromptCompany        = fake()->company();
    $this->userPromptManager        = fake()->name();
    $this->userPromptDateFormat     = DateFormatEnum::VARIANT_A;
    $this->userPromptLanguage       = LanguageEnum::EN_GB;

    $this->optionalProperties = [
        'problem_solving_text' => 1,
        'growth_interest_text' => 2,
        'unique_value_text'    => 3,
        'achievements_text'    => 4,
        'motivation_text'      => 5,
        'career_goals'         => 6,
        'other_details'        => 7,
    ];
});

test(description: 'role returns user', closure: function (): void
{
    expect(value: new UserPrompt(settings: [])->role())
        ->toBe(expected: 'user');
});

test(description: 'build returns a string containing all required prompts', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'             => $this->userPromptName,
        'job_title'        => $this->userPromptJobTitle,
        'job_description'  => $this->userPromptJobDescription,
        'company'          => $this->userPromptCompany,
        'manager'          => $this->userPromptManager,
        'date_format'      => $this->userPromptDateFormat,
        'language_variant' => $this->userPromptLanguage,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptName)
        ->toContain(needles: $this->userPromptJobTitle)
        ->toContain(needles: $this->userPromptCompany)
        ->toContain(needles: $this->userPromptManager)
        ->toContain(needles: '2025')
        ->toContain(needles: $this->userPromptLanguage->label())
        ->toContain(needles: trans(key: 'prompt.user.request'));
});

test(description: 'build handles missing optional settings with defaults', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name' => $this->userPromptName,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptName)
        ->toContain(needles: trans(key: 'prompt.user.request'));
});

test(description: 'build uses provided language variant', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'             => $this->userPromptName,
        'language_variant' => LanguageEnum::EN_US,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LanguageEnum::EN_US->label());
});

test(description: 'build includes optional text properties if provided by the user', closure: function (): void
{
    collect(value: $this->optionalProperties)->each(callback: function ($enumKey, $fieldName): void
    {
        $prompt = new UserPrompt(settings: [
            'name'     => $this->userPromptName,
            $fieldName => fake()->paragraph(),
        ]);

        expect(value: $prompt->build())
            ->toBeString()
            ->toContain(
                needles: PromptOptionEnum::from(value: $enumKey)->userPrompt(text: '')
            );
    });
});

test(description: 'build does not include optional text properties if not provided by the user', closure: function (): void
{
    collect(value: $this->optionalProperties)->each(callback: function ($enumKey, $fieldName): void
    {
        $prompt = new UserPrompt(settings: [
            'name'     => $this->userPromptName,
            $fieldName => null,
        ]);

        expect(value: $prompt->build())
            ->toBeString()
            ->not->toContain(
                needles: PromptOptionEnum::from(value: $enumKey)->userPrompt('')
            );
    });
});
