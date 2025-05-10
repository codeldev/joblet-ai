<?php

declare(strict_types=1);

/** @noinspection PhpUnused */

namespace App\Livewire\Forms\Generator;

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class GeneratorForm extends Form
{
    #[Validate(['required', 'integer', new Enum(type: LetterCreativityEnum::class)])]
    public int $option_creativity = 2;

    #[Validate(['required', 'integer', new Enum(type: LetterToneEnum::class)])]
    public int $option_tone = 2;

    #[Validate(['required', 'integer', new Enum(type: LetterLengthEnum::class)])]
    public int $option_length = 2;

    #[Validate(['required', 'integer', new Enum(type: LanguageEnum::class)])]
    public int $language_variant = 1;

    #[Validate(['required', 'integer', new Enum(type: DateFormatEnum::class)])]
    public int $date_format = 1;

    #[Validate(['required', 'string', 'min:5', 'max:255'])]
    public ?string $name = null;

    #[Validate(['required', 'string', 'min:5', 'max:255'])]
    public ?string $job_title = null;

    #[Validate(['required', 'string', 'min:50', 'max:10000'])]
    public ?string $job_description = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:255'])]
    public ?string $company = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:255'])]
    public ?string $manager = null;

    #[Validate(['required', 'boolean'])]
    public bool $include_placeholders = false;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $problem_solving_text = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $growth_interest_text = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $unique_value_text = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $achievements_text = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $motivation_text = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $career_goals = null;

    #[Validate(['nullable', 'string', 'min:5', 'max:5000'])]
    public ?string $other_details = null;
}
