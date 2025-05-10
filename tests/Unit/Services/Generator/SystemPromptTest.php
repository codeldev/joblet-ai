<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterStyleEnum;
use App\Enums\LetterToneEnum;
use App\Enums\PromptOptionEnum;
use App\Services\Generator\SystemPrompt;
use Carbon\CarbonImmutable;

beforeEach(closure: function (): void
{
    $this->systemPromptName       = fake()->name();
    $this->systemPromptJob        = fake()->jobTitle();
    $this->systemPromptCompany    = fake()->company();
    $this->systemPromptManager    = fake()->name();
    $this->systemPromptLeaveDate  = CarbonImmutable::parse(time: '2025-04-25');
    $this->systemPromptDateFormat = DateFormatEnum::VARIANT_A;
    $this->systemPromptLanguage   = LanguageEnum::EN_GB;
    $this->systemPromptReason     = 'Career growth';
    $this->systemPromptExperience = 'Great team';
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
        'job'              => $this->systemPromptJob,
        'company'          => $this->systemPromptCompany,
        'manager'          => $this->systemPromptManager,
        'leave_date'       => $this->systemPromptLeaveDate,
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

test(description: 'build includes gratitude option when flag is true', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'              => $this->systemPromptName,
        'express_gratitude' => true,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: PromptOptionEnum::GRATITUDE->text());
});

test(description: 'build does not include gratitude option when flag is false', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'              => $this->systemPromptName,
        'express_gratitude' => false,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::GRATITUDE->text());
});

test(description: 'build includes reason option when flag is true and text is provided', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                => $this->systemPromptName,
        'leaving_reason'      => true,
        'leaving_reason_text' => $this->systemPromptReason,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: PromptOptionEnum::REASON->text());
});

test(description: 'build does not include reason option when flag is false', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                => $this->systemPromptName,
        'leaving_reason'      => false,
        'leaving_reason_text' => $this->systemPromptReason,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::REASON->text());
});

test(description: 'build does not include reason option when text is empty', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                => $this->systemPromptName,
        'leaving_reason'      => true,
        'leaving_reason_text' => '',
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::REASON->text());
});

test(description: 'build includes experience option when flag is true and text is provided', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                     => $this->systemPromptName,
        'positive_experience'      => true,
        'positive_experience_text' => $this->systemPromptExperience,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: PromptOptionEnum::EXPERIENCE->text());
});

test(description: 'build does not include experience option when flag is false', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                     => $this->systemPromptName,
        'positive_experience'      => false,
        'positive_experience_text' => $this->systemPromptExperience,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::EXPERIENCE->text());
});

test(description: 'build does not include experience option when text is empty', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                     => $this->systemPromptName,
        'positive_experience'      => true,
        'positive_experience_text' => '',
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::EXPERIENCE->text());
});

test(description: 'build includes transition option when flag is true', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                  => $this->systemPromptName,
        'transition_assistance' => true,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: PromptOptionEnum::TRANSITION->text());
});

test(description: 'build does not include transition option when flag is false', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'                  => $this->systemPromptName,
        'transition_assistance' => false,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: PromptOptionEnum::TRANSITION->text());
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

test(description: 'build uses provided date format', closure: function (): void
{
    $prompt = new SystemPrompt(settings: [
        'name'        => $this->systemPromptName,
        'leave_date'  => $this->systemPromptLeaveDate,
        'date_format' => DateFormatEnum::VARIANT_B,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: DateFormatEnum::VARIANT_B->format())
        ->toContain(needles: now()->format(format: DateFormatEnum::VARIANT_B->format()));
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
