<?php

declare(strict_types=1);

namespace App\Services\Generator;

use App\Abstracts\Services\Generator\Prompt;
use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterStyleEnum;
use App\Enums\LetterToneEnum;
use App\Enums\PromptOptionEnum;

final class SystemPrompt extends Prompt
{
    /** @var array<int, string> */
    private array $prompts = [];

    /** @var array<int, string> */
    private array $important = [];

    /** @var array<int, string> */
    private array $formatting = [];

    public function role(): string
    {
        return 'system';
    }

    public function build(): string
    {
        return $this
            ->setIntroAndGuideLines()
            ->setImportantRules()
            ->setFormattingRules()
            ->formatted();
    }

    private function formatted(): string
    {
        return collect(value: [
            collect(value: $this->prompts)->implode(value: PHP_EOL),
            collect(value: $this->important)->implode(value: PHP_EOL),
            collect(value: $this->formatting)->implode(value: PHP_EOL),
        ])->implode(value: PHP_EOL);
    }

    private function setIntroAndGuideLines(): self
    {
        $this
            ->setStyleText()
            ->setGuideLines()
            ->setLengthText()
            ->setToneText()
            ->setLetterOptionText(settingKey: 'problem_solving_text', enumKey: 1)
            ->setLetterOptionText(settingKey: 'growth_interest_text', enumKey: 2)
            ->setLetterOptionText(settingKey: 'unique_value_text', enumKey: 3)
            ->setLetterOptionText(settingKey: 'achievements_text', enumKey: 4)
            ->setLetterOptionText(settingKey: 'motivation_text', enumKey: 5)
            ->setLetterOptionText(settingKey: 'career_goals', enumKey: 6)
            ->setLetterOptionText(settingKey: 'other_details', enumKey: 7);

        return $this;
    }

    private function setGuideLines(): self
    {
        $this->prompts[] = trans(key: 'prompt.system.guidelines.title');
        $this->prompts[] = trans(key: 'prompt.system.guidelines.line1');
        $this->prompts[] = trans(key: 'prompt.system.guidelines.line2');
        $this->prompts[] = trans(key: 'prompt.system.guidelines.line3');

        return $this;
    }

    private function setStyleText(): self
    {
        /** @var int|string $optionTone */
        $optionTone = $this->settings['option_tone'] ?? LetterStyleEnum::FORMAL->value;

        $this->prompts[] = LetterStyleEnum::from(
            value: $optionTone
        )->text() . PHP_EOL;

        return $this;
    }

    private function setToneText(): self
    {
        /** @var int|string $optionTone */
        $optionTone = $this->settings['option_tone'] ?? LetterToneEnum::FORMAL->value;

        $this->prompts[] = LetterToneEnum::from(
            value: $optionTone
        )->text();

        return $this;
    }

    private function setLengthText(): self
    {
        /** @var int|string $optionLength */
        $optionLength = $this->settings['option_length'] ?? LetterLengthEnum::MEDIUM->value;

        $this->prompts[] = LetterLengthEnum::from(
            value: $optionLength
        )->text();

        return $this;
    }

    private function setLetterOptionText(string $settingKey, int $enumKey): self
    {
        /** @var string|null $text */
        $text = $this->settings[$settingKey] ?? null;

        if (notEmpty(value: $text))
        {
            $this->prompts[] = PromptOptionEnum::from(value: $enumKey)->systemPrompt();
        }

        return $this;
    }

    private function setImportantRules(): self
    {
        /** @var LanguageEnum $languageVariant */
        $languageVariant = $this->settings['language_variant'] ?? LanguageEnum::EN_GB;

        /** @var DateFormatEnum $dateFormat */
        $dateFormat = $this->settings['date_format'] ?? DateFormatEnum::VARIANT_A;

        $dateFormatString = $dateFormat->format();

        $this->important = [
            trans(key: 'prompt.system.important.title'),
            trans(key: 'prompt.system.important.line1', replace: [
                'lang' => $languageVariant->label(),
            ]),
            trans(key: 'prompt.system.important.line2', replace: [
                'format'  => $dateFormatString,
                'example' => now()->format(format: $dateFormatString),
            ]),
            trans(key: 'prompt.system.important.line3'),
            trans(key: 'prompt.system.important.line4'),
            trans(key: 'prompt.system.important.line5'),
            trans(key: 'prompt.system.important.line6'),
        ];

        return $this;
    }

    private function setFormattingRules(): self
    {
        /** @var bool $includePlaceholders */
        $includePlaceholders = $this->settings['include_placeholders'] ?? false;

        $includePlaceholders
            ? $this->formattingWithPlaceholders()
            : $this->formattingWithoutPlaceholders();

        return $this;
    }

    private function formattingWithPlaceholders(): void
    {
        /** @var DateFormatEnum $dateFormat */
        $dateFormat = $this->settings['date_format'] ?? DateFormatEnum::VARIANT_A;

        $dateFormatString = $dateFormat->format();

        $this->formatting = [
            trans(key: 'prompt.system.placeholders.title'),
            trans(key: 'prompt.system.placeholders.with.line1'),
            trans(key: 'prompt.system.placeholders.with.line2', replace: [
                'format'  => $dateFormatString,
                'example' => now()->format(format: $dateFormatString),
            ]),
            trans(key: 'prompt.system.placeholders.with.line3'),
            trans(key: 'prompt.system.placeholders.with.line4'),
        ];
    }

    private function formattingWithoutPlaceholders(): void
    {
        /** @var DateFormatEnum $dateFormat */
        $dateFormat = $this->settings['date_format'] ?? DateFormatEnum::VARIANT_A;

        $dateFormatString = $dateFormat->format();

        $this->formatting = [
            trans(key: 'prompt.system.placeholders.title'),
            trans(key: 'prompt.system.placeholders.none.line1', replace: [
                'format'  => $dateFormatString,
                'example' => now()->format(format: $dateFormatString),
            ]),
            trans(key: 'prompt.system.placeholders.none.line2'),
            trans(key: 'prompt.system.placeholders.none.line3'),
        ];
    }
}
