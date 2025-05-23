<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Models;

use App\Dto\PostContentDTO;
use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Support\BlogPostSlugOptions;
use App\Support\MarkdownToHtml;
use Carbon\CarbonImmutable;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Override;
use Spatie\Sluggable\HasSlug;

/**
 * @property-read string $id
 * @property-read string $idea_id
 * @property-read string $prompt_id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string $description
 * @property-read string $summary
 * @property-read string $content
 * @property-read int $status
 * @property-read null|CarbonImmutable $published_at
 * @property-read null|CarbonImmutable $scheduled_at
 * @property-read bool $has_featured_image
 * @property-read int $word_count
 * @property-read int $read_time
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read BlogPrompt $prompt
 * @property-read BlogIdea $idea
 * @property-read BlogImage $featured
 * @property-read Collection<int, BlogImage> $images
 */
final class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory;

    /** @see HasSlug */
    use HasSlug;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'blog_posts';

    /** @var array<string, string> */
    protected $casts = [
        'status'             => PostStatusEnum::class,
        'published_at'       => 'immutable_datetime',
        'scheduled_at'       => 'immutable_datetime',
        'has_featured_image' => 'boolean',
    ];

    #[Override]
    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->where(
            column  : 'status',
            operator: '=',
            value   : PostStatusEnum::PUBLISHED
        )->first();
    }

    public function getSlugOptions(): BlogPostSlugOptions
    {
        /** @var BlogPostSlugOptions $options */
        $options = BlogPostSlugOptions::create()
            ->generateSlugsFrom(fieldName: 'title')
            ->saveSlugsTo(fieldName: 'slug')
            ->usingSeparator(separator: '-')
            ->slugsShouldBeNoLongerThan(maximumLength: 75)
            ->doNotGenerateSlugsOnUpdate();

        return $options->withoutStopWords();
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<BlogPrompt, $this> */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(
            related   : BlogPrompt::class,
            foreignKey: 'prompt_id',
            ownerKey  : 'id'
        );
    }

    /** @return BelongsTo<BlogIdea, $this> */
    public function idea(): BelongsTo
    {
        return $this->belongsTo(
            related   : BlogIdea::class,
            foreignKey: 'idea_id',
            ownerKey  : 'id'
        );
    }

    /** @return HasOne<BlogImage, $this> */
    public function featured(): HasOne
    {
        return $this->hasOne(
            related    : BlogImage::class,
            foreignKey : 'post_id',
            localKey   : 'id'
        )->where(
            column   : 'type',
            operator : '=',
            value    : BlogImageTypeEnum::FEATURED
        );
    }

    /** @return HasMany<BlogImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(
            related    : BlogImage::class,
            foreignKey : 'post_id',
            localKey   : 'id'
        );
    }

    public function featuredImagePath(string $file): string
    {
        return trans(key: ':post/featured/:file', replace: [
            'post' => $this->id,
            'file' => $file,
        ]);
    }

    public function tempImagePath(string $file, string $id): string
    {
        return trans(key: ':post/temp/:id/:file', replace: [
            'post' => $this->id,
            'file' => $file,
            'id'   => $id,
        ]);
    }

    public function contentImagePath(string $id): string
    {
        return trans(key: ':post/content/:id/', replace: [
            'post' => $this->id,
            'id'   => $id,
        ]);
    }

    /** @return Attribute<PostContentDTO, never> */
    public function markdownHtml(): Attribute
    {
        return Attribute::make(get: function (): PostContentDTO
        {
            /** @var PostContentDTO $result */
            $result = Cache::remember(
                key     : "blog:post:{$this->id}",
                ttl     : now()->addMonth(),
                callback: fn (): PostContentDTO => new MarkdownToHtml(blogPost: $this)->convert()
            );

            return $result;
        });
    }
}
