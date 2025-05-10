<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Services\Generator\UserPrompt;
use Carbon\CarbonImmutable;

beforeEach(closure: function (): void
{
    $this->userPromptName       = fake()->name();
    $this->userPromptJob        = fake()->jobTitle();
    $this->userPromptCompany    = fake()->company();
    $this->userPromptManager    = fake()->name();
    $this->userPromptLeaveDate  = CarbonImmutable::parse(time: '2025-04-25');
    $this->userPromptDateFormat = DateFormatEnum::VARIANT_A;
    $this->userPromptLanguage   = LanguageEnum::EN_GB;
    $this->userPromptReason     = 'Career growth';
    $this->userPromptExperience = 'Great team';
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
        'job'              => $this->userPromptJob,
        'company'          => $this->userPromptCompany,
        'manager'          => $this->userPromptManager,
        'leave_date'       => $this->userPromptLeaveDate,
        'date_format'      => $this->userPromptDateFormat,
        'language_variant' => $this->userPromptLanguage,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptName)
        ->toContain(needles: $this->userPromptJob)
        ->toContain(needles: $this->userPromptCompany)
        ->toContain(needles: $this->userPromptManager)
        ->toContain(needles: '2025')
        ->toContain(needles: $this->userPromptLanguage->label())
        ->toContain(needles: trans(key: 'prompt.user.request'));
});

test(description: 'build includes leaving reason when provided', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                => $this->userPromptName,
        'job'                 => $this->userPromptJob,
        'company'             => $this->userPromptCompany,
        'manager'             => $this->userPromptManager,
        'leaving_reason'      => true,
        'leaving_reason_text' => $this->userPromptReason,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptReason);
});

test(description: 'build does not include leaving reason when flag is false', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                => $this->userPromptName,
        'job'                 => $this->userPromptJob,
        'company'             => $this->userPromptCompany,
        'manager'             => $this->userPromptManager,
        'leaving_reason'      => false,
        'leaving_reason_text' => $this->userPromptReason,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: $this->userPromptReason);
});

test(description: 'build does not include leaving reason when text is empty', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                => $this->userPromptName,
        'job'                 => $this->userPromptJob,
        'company'             => $this->userPromptCompany,
        'manager'             => $this->userPromptManager,
        'leaving_reason'      => true,
        'leaving_reason_text' => '',
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: trans(key: 'prompt.user.info.reason', replace: [
            'reason' => '',
        ]));
});

test(description: 'build includes positive experience when provided', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                     => $this->userPromptName,
        'job'                      => $this->userPromptJob,
        'company'                  => $this->userPromptCompany,
        'manager'                  => $this->userPromptManager,
        'positive_experience'      => true,
        'positive_experience_text' => $this->userPromptExperience,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptExperience);
});

test(description: 'build does not include positive experience when flag is false', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                     => $this->userPromptName,
        'job'                      => $this->userPromptJob,
        'company'                  => $this->userPromptCompany,
        'manager'                  => $this->userPromptManager,
        'positive_experience'      => false,
        'positive_experience_text' => $this->userPromptExperience,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: $this->userPromptExperience);
});

test(description: 'build does not include positive experience when text is empty', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                     => $this->userPromptName,
        'job'                      => $this->userPromptJob,
        'company'                  => $this->userPromptCompany,
        'manager'                  => $this->userPromptManager,
        'positive_experience'      => true,
        'positive_experience_text' => '',
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->not->toContain(needles: trans(key: 'prompt.user.info.experience', replace: [
            'experience' => '',
        ]));
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

test(description: 'build handles both reason and experience when provided', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'                     => $this->userPromptName,
        'job'                      => $this->userPromptJob,
        'company'                  => $this->userPromptCompany,
        'manager'                  => $this->userPromptManager,
        'leaving_reason'           => true,
        'leaving_reason_text'      => $this->userPromptReason,
        'positive_experience'      => true,
        'positive_experience_text' => $this->userPromptExperience,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptReason)
        ->toContain(needles: $this->userPromptExperience);
});

test(description: 'build uses provided date format', closure: function (): void
{
    $prompt = new UserPrompt(settings: [
        'name'        => $this->userPromptName,
        'leave_date'  => $this->userPromptLeaveDate,
        'date_format' => DateFormatEnum::VARIANT_B,
    ]);

    expect(value: $prompt->build())
        ->toBeString()
        ->toContain(needles: $this->userPromptLeaveDate->format(format: DateFormatEnum::VARIANT_B->format()));
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
