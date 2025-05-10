<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterStyleEnum;
use App\Enums\LetterToneEnum;
use App\Services\Generator\SystemPrompt;

use function Pest\Laravel\actingAs;

beforeEach(closure: function (): void
{
    actingAs(user: testUser());

    $this->systemPromptName           = fake()->name();
    $this->systemPromptJobTitle       = fake()->jobTitle();
    $this->systemPromptJobDescription = fake()->paragraphs(nb: 5, asText: true);
    $this->systemPromptCompany        = fake()->company();
    $this->systemPromptManager        = fake()->name();
    $this->systemPromptDateFormat     = DateFormatEnum::VARIANT_A;
    $this->systemPromptLanguage       = LanguageEnum::EN_GB;
});

test(description: 'role returns system', closure: function (): void
{
    expect(value: new SystemPrompt(settings: [])->role())
        ->toBe(expected: 'system');
});

test(description: 'build returns a string containing all required prompts', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'             => $this->systemPromptName,
        'job_title'        => $this->systemPromptJobTitle,
        'job_description'  => $this->systemPromptJobDescription,
        'company'          => $this->systemPromptCompany,
        'manager'          => $this->systemPromptManager,
        'date_format'      => $this->systemPromptDateFormat,
        'language_variant' => $this->systemPromptLanguage,
        'option_style'     => LetterStyleEnum::FORMAL->value,
        'option_tone'      => LetterToneEnum::FORMAL->value,
        'option_length'    => LetterLengthEnum::MEDIUM->value,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LetterStyleEnum::FORMAL->text())
        ->toContain(needles: LetterToneEnum::FORMAL->text())
        ->toContain(needles: LetterLengthEnum::MEDIUM->text())
        ->toContain(needles: trans(key: 'prompt.system.guidelines.title'))
        ->toContain(needles: $this->systemPromptLanguage->label())
        ->toContain(needles: trans(key: 'prompt.system.important.title'))
        ->toContain(needles: trans(key: 'prompt.system.placeholders.title'));
});

test(description: 'build uses provided letter style', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'        => $this->systemPromptName,
        'option_tone' => LetterStyleEnum::CASUAL->value,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LetterStyleEnum::CASUAL->text());
});

test(description: 'build uses provided letter tone', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'        => $this->systemPromptName,
        'option_tone' => LetterToneEnum::CASUAL->value,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LetterToneEnum::CASUAL->text());
});

test(description: 'build uses provided letter length', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'          => $this->systemPromptName,
        'option_length' => LetterLengthEnum::SHORT->value,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LetterLengthEnum::SHORT->text());
});

test(description: 'build uses provided language variant', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'             => $this->systemPromptName,
        'language_variant' => LanguageEnum::EN_US,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LanguageEnum::EN_US->label())
        ->not->toContain(needles: LanguageEnum::EN_GB->label());
});

test(description: 'build includes placeholders when flag is true', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                 => $this->systemPromptName,
        'include_placeholders' => true,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: trans(key: 'prompt.system.placeholders.with.line1'))
        ->not->toContain(needles: trans(key: 'prompt.system.placeholders.none.line1', replace: [
            'format'  => DateFormatEnum::VARIANT_A->format(),
            'example' => now()->format(format: DateFormatEnum::VARIANT_A->format()),
        ]));
});

test(description: 'build does not include placeholders when flag is false', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                 => $this->systemPromptName,
        'include_placeholders' => false,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: trans(key: 'prompt.system.placeholders.with.line1'))
        ->toContain(needles: trans(key: 'prompt.system.placeholders.none.line1', replace: [
            'format'  => DateFormatEnum::VARIANT_A->format(),
            'example' => now()->format(format: DateFormatEnum::VARIANT_A->format()),
        ]));
});

test(description: 'build handles missing optional settings with defaults', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name' => $this->systemPromptName,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: LetterStyleEnum::FORMAL->text())
        ->toContain(needles: LetterToneEnum::FORMAL->text())
        ->toContain(needles: LetterLengthEnum::MEDIUM->text())
        ->toContain(needles: DateFormatEnum::VARIANT_A->format())
        ->toContain(needles: LanguageEnum::EN_GB->label());
});
