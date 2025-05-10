<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use App\Models\Generated;
use App\Models\Usage;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test(description: 'generated table has expected fields', closure: function (): void
{
    $schema = Schema::getColumnListing(
        table: (new Generated)->getTable()
    );

    expect(value: $schema)->toBe(expected: [
        'id',
        'user_id',
        'name',
        'job',
        'company',
        'manager',
        'leave_date',
        'leaving_reason',
        'transition_assistance',
        'express_gratitude',
        'positive_experience',
        'include_placeholders',
        'leaving_reason_text',
        'positive_experience_text',
        'language_variant',
        'date_format',
        'option_creativity',
        'option_tone',
        'option_length',
        'generated_content_raw',
        'generated_content_html',
        'created_at',
        'updated_at',
    ]);
});

test(description: 'generated has correct casts', closure: function (): void
{
    expect(value: (new Generated)->getCasts())->toMatchArray(array: [
        'name'                     => 'encrypted',
        'job'                      => 'encrypted',
        'company'                  => 'encrypted',
        'manager'                  => 'encrypted',
        'leave_date'               => 'immutable_date',
        'leaving_reason_text'      => 'encrypted',
        'positive_experience_text' => 'encrypted',
        'language_variant'         => LanguageEnum::class,
        'date_format'              => DateFormatEnum::class,
        'option_creativity'        => LetterCreativityEnum::class,
        'option_tone'              => LetterToneEnum::class,
        'option_length'            => LetterLengthEnum::class,
    ]);
});

test(description: 'generated belongs to user', closure: function (): void
{
    expect(value: (new Generated)->user()->getRelated())
        ->toBeInstanceOf(class: User::class);
});

test(description: 'generated has one credit (usage)', closure: function (): void
{
    expect(value: (new Generated)->credit()->getRelated())
        ->toBeInstanceOf(class: Usage::class);
});
