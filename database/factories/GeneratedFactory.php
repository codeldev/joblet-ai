<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use App\Models\Generated;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Generated> */
final class GeneratedFactory extends Factory
{
    /** @var class-string<Generated> */
    protected $model = Generated::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $content = collect(value: [1, 2, 3, 4])
            ->map(callback: fn () => fake()->paragraph(nbSentences: 4))
            ->implode(value: "\n\n");

        return [
            'user_id'                  => User::factory(),
            'name'                     => fake()->name(),
            'job'                      => fake()->jobTitle(),
            'company'                  => fake()->company(),
            'manager'                  => fake()->name(),
            'leave_date'               => now()->addDays(value: fake()->numberBetween(int1: 14, int2: 90)),
            'leaving_reason'           => $leavingReason = fake()->boolean(),
            'transition_assistance'    => fake()->boolean(),
            'express_gratitude'        => fake()->boolean(),
            'positive_experience'      => $positiveExperience = fake()->boolean(),
            'include_placeholders'     => fake()->boolean(),
            'leaving_reason_text'      => $leavingReason ? fake()->text() : null,
            'positive_experience_text' => $positiveExperience ? fake()->text() : null,
            'language_variant'         => fake()->randomElement(array: LanguageEnum::cases()),
            'date_format'              => fake()->randomElement(array: DateFormatEnum::cases()),
            'option_creativity'        => fake()->randomElement(array: LetterCreativityEnum::cases()),
            'option_tone'              => fake()->randomElement(array: LetterToneEnum::cases()),
            'option_length'            => fake()->randomElement(array: LetterLengthEnum::cases()),
            'generated_content_raw'    => $content,
            'generated_content_html'   => nl2br(string: $content),
        ];
    }
}
