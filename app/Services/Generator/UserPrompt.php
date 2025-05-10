<?php

declare(strict_types=1);

namespace App\Services\Generator;

use App\Abstracts\Services\Generator\Prompt;
use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\PromptOptionEnum;

final class UserPrompt extends Prompt
{
    /** @var array<int, string> */
    private array $prompts = [];

    public function role(): string
    {
        return 'user';
    }

    public function build(): string
    {
        return $this
            ->addStandardData()
            ->addUserData(settingKey: 'problem_solving_text', enumKey: 1)
            ->addUserData(settingKey: 'growth_interest_text', enumKey: 2)
            ->addUserData(settingKey: 'unique_value_text', enumKey: 3)
            ->addUserData(settingKey: 'achievements_text', enumKey: 4)
            ->addUserData(settingKey: 'motivation_text', enumKey: 5)
            ->addUserData(settingKey: 'career_goals', enumKey: 6)
            ->addUserData(settingKey: 'other_details', enumKey: 7)
            ->addCVData()
            ->finalise()
            ->output();
    }

    private function output(): string
    {
        return collect(value: $this->prompts)
            ->implode(value: PHP_EOL);
    }

    private function addStandardData(): self
    {
        /** @var string $name */
        $name = $this->settings['name'] ?? '';

        /** @var string $jobTitle */
        $jobTitle = $this->settings['job_title'] ?? '';

        /** @var string $jobDescription */
        $jobDescription = $this->settings['job_description'] ?? '';

        /** @var string $company */
        $company = $this->settings['company'] ?? '';

        /** @var string $manager */
        $manager = $this->settings['manager'] ?? '';

        /** @var DateFormatEnum $dateFormat */
        $dateFormat = $this->settings['date_format'] ?? DateFormatEnum::VARIANT_A;

        /** @var LanguageEnum $languageVariant */
        $languageVariant = $this->settings['language_variant'] ?? LanguageEnum::EN_GB;

        $this->prompts = [
            trans(key: 'prompt.user.info.name', replace: [
                'name' => $name,
            ]),
            trans(key: 'prompt.user.info.job.title', replace: [
                'title' => $jobTitle,
            ]),
            trans(key: 'prompt.user.info.company', replace: [
                'name' => $company,
            ]),
            trans(key: 'prompt.user.info.manager', replace: [
                'name' => $manager,
            ]),
            trans(key: 'prompt.user.info.date.current', replace: [
                'date' => now()->format(format: $dateFormat->format()),
            ]),
            trans(key: 'prompt.user.info.language', replace: [
                'lang' => $languageVariant->label(),
            ]),
            trans(key: 'prompt.user.info.job.description') . PHP_EOL . PHP_EOL . $jobDescription,
        ];

        return $this;
    }

    private function addUserData(string $settingKey, int $enumKey): self
    {
        /** @var string|null $text */
        $text = $this->settings[$settingKey] ?? null;

        if (notEmpty(value: $text))
        {
            /** @var string $text */
            $this->prompts[] = PromptOptionEnum::from(value: $enumKey)->userPrompt(text: $text);
        }

        return $this;
    }

    private function addCVData(): self
    {
        $this->prompts[] = trans(key: 'prompt.user.info.cv') . PHP_EOL . $this->user->cv_content;

        return $this;
    }

    private function finalise(): self
    {
        $this->prompts[] = PHP_EOL . trans(key: 'prompt.user.request');

        return $this;
    }
}
