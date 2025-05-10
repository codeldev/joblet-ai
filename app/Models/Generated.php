<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use Carbon\CarbonImmutable;
use Database\Factories\GeneratedFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $name
 * @property-read string $job_title
 * @property-read string $job_description
 * @property-read null|string $company
 * @property-read null|string $manager
 * @property-read null|string $problem_solving_text
 * @property-read null|string $growth_interest_text
 * @property-read null|string $unique_value_text
 * @property-read null|string $achievements_text
 * @property-read null|string $motivation_text
 * @property-read null|string $career_goals
 * @property-read null|string $other_details
 * @property-read bool $include_placeholders
 * @property-read LanguageEnum $language_variant
 * @property-read DateFormatEnum $date_format
 * @property-read LetterCreativityEnum $option_creativity
 * @property-read LetterToneEnum $option_tone
 * @property-read LetterLengthEnum $option_length
 * @property-read string $generated_content_raw
 * @property-read string $generated_content_html
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read User $user
 * @property-read Usage $credit
 */
final class Generated extends Model
{
    /** @use HasFactory<GeneratedFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'generated';

    /** @var array<string, string> */
    protected $casts = [
        'name'                   => 'encrypted',
        'job_title'              => 'encrypted',
        'job_description'        => 'encrypted',
        'company'                => 'encrypted',
        'manager'                => 'encrypted',
        'problem_solving_text'   => 'encrypted',
        'growth_interest_text'   => 'encrypted',
        'unique_value_text'      => 'encrypted',
        'achievements_text'      => 'encrypted',
        'motivation_text'        => 'encrypted',
        'career_goals'           => 'encrypted',
        'other_details'          => 'encrypted',
        'include_placeholders'   => 'boolean',
        'generated_content_raw'  => 'encrypted',
        'generated_content_html' => 'encrypted',
        'language_variant'       => LanguageEnum::class,
        'date_format'            => DateFormatEnum::class,
        'option_creativity'      => LetterCreativityEnum::class,
        'option_tone'            => LetterToneEnum::class,
        'option_length'          => LetterLengthEnum::class,
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class
        );
    }

    /** @return HasOne<Usage, $this> */
    public function credit(): HasOne
    {
        return $this->hasOne(
            related   : Usage::class,
            foreignKey: 'generated_id'
        );
    }
}
