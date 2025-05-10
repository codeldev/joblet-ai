<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use App\Models\Generated;
use App\Services\Models\GeneratedService;

it('returns fillable fields except unfillable and with enum values', function (): void
{
    $generated = Generated::factory()->create(attributes: [
        'language_variant'  => LanguageEnum::EN_US,
        'date_format'       => DateFormatEnum::VARIANT_C,
        'option_length'     => LetterLengthEnum::LONG,
        'option_tone'       => LetterToneEnum::PRO,
        'option_creativity' => LetterCreativityEnum::CREATIVE,
    ]);

    $fillable = GeneratedService::getFillable(asset: $generated);

    expect(value: $fillable['language_variant'])
        ->toBe(expected: LanguageEnum::EN_US->value)
        ->and(value: $fillable['date_format'])
        ->toBe(expected: DateFormatEnum::VARIANT_C->value)
        ->and(value: $fillable['option_length'])
        ->toBe(expected: LetterLengthEnum::LONG->value)
        ->and(value: $fillable['option_tone'])
        ->toBe(expected: LetterToneEnum::PRO->value)
        ->and(value: $fillable['option_creativity'])
        ->toBe(expected: LetterCreativityEnum::CREATIVE->value)
        ->and(value: $fillable)
        ->toHaveKey(key: 'name')
        ->and(value: $fillable)
        ->toHaveKey(key: 'job_title')
        ->and(value: $fillable)
        ->toHaveKey(key: 'company')
        ->and(value: $fillable)
        ->toHaveKey(key: 'manager');
});
