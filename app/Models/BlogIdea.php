<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\BlogIdeaFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $id
 * @property-read string $topic
 * @property-read string $keywords
 * @property-read string $focus
 * @property-read string $requirements
 * @property-read string $additional
 * @property-read CarbonImmutable $schedule_date
 * @property-read null|CarbonImmutable $queued_at
 * @property-read null|CarbonImmutable $processed_at
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read BlogPost $post
 */
final class BlogIdea extends Model
{
    /** @use HasFactory<BlogIdeaFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'blog_ideas';

    /** @var array<string, string> */
    protected $casts = [
        'schedule_date' => 'immutable_datetime',
        'queued_at'     => 'immutable_datetime',
        'processed_at'  => 'immutable_datetime',
    ];

    /** @return HasOne<BlogPost, $this> */
    public function post(): HasOne
    {
        return $this->hasOne(
            related   : BlogPost::class,
            foreignKey: 'idea_id',
            localKey  : 'id'
        );
    }
}
