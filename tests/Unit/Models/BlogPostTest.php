<?php

/** @noinspection HtmlUnknownAnchorTarget */
/** @noinspection NullPointerExceptionInspection */

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Dto\PostContentDTO;
use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Models\BlogIdea;
use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Models\BlogPrompt;
use App\Support\BlogPostSlugOptions;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

describe(description: 'BlogPost Model', tests: function (): void
{
    it('can be created using the factory', function (): void
    {
        $blogPost = BlogPost::factory()
            ->create();

        expect(value: $blogPost)
            ->toBeInstanceOf(class: BlogPost::class)
            ->and(value: $blogPost->id)
            ->not->toBeEmpty()
            ->and(value: $blogPost->idea_id)
            ->not->toBeEmpty()
            ->and(value: $blogPost->prompt_id)
            ->not->toBeEmpty()
            ->and(value: $blogPost->title)
            ->not->toBeEmpty()
            ->and(value: $blogPost->slug)
            ->not->toBeEmpty()
            ->and(value: $blogPost->description)
            ->not->toBeEmpty()
            ->and(value: $blogPost->summary)
            ->not->toBeEmpty()
            ->and(value: $blogPost->content)
            ->not->toBeEmpty();
    });

    it('casts status to PostStatusEnum', function (): void
    {
        $blogPost = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PUBLISHED,
        ]);

        expect(value: $blogPost->status)
            ->toBeInstanceOf(class: PostStatusEnum::class)
            ->and(value: $blogPost->status)
            ->toBe(expected: PostStatusEnum::PUBLISHED);
    });

    it('casts dates to immutable datetime objects', function (): void
    {
        $blogPost = BlogPost::factory()->create(attributes: [
            'published_at' => now(),
            'scheduled_at' => now(),
        ]);

        expect(value: $blogPost->published_at)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogPost->scheduled_at)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogPost->created_at)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogPost->updated_at)
            ->toBeInstanceOf(class: CarbonImmutable::class);
    });

    it('casts has_featured_image to boolean', function (): void
    {
        $blogPost = BlogPost::factory()->create(attributes: [
            'has_featured_image' => true,
        ]);

        expect(value: $blogPost->has_featured_image)
            ->toBeTrue();

        $blogPost = BlogPost::factory()->create(attributes: [
            'has_featured_image' => false,
        ]);

        expect(value: $blogPost->has_featured_image)
            ->toBeFalse();
    });

    it('belongs to a blog idea', function (): void
    {
        $blogIdea = BlogIdea::factory()->create();
        $blogPost = BlogPost::factory()->create(attributes: [
            'idea_id' => $blogIdea->id,
        ]);

        expect(value: $blogPost->idea())
            ->toBeInstanceOf(class: BelongsTo::class)
            ->and(value: $blogPost->idea)
            ->toBeInstanceOf(class: BlogIdea::class)
            ->and(value: $blogPost->idea->id)
            ->toBe(expected: $blogIdea->id);
    });

    it('belongs to a blog prompt', function (): void
    {
        $blogPrompt = BlogPrompt::factory()->create();
        $blogPost   = BlogPost::factory()->create(attributes: [
            'prompt_id' => $blogPrompt->id,
        ]);

        expect(value: $blogPost->prompt())
            ->toBeInstanceOf(class: BelongsTo::class)
            ->and(value: $blogPost->prompt)
            ->toBeInstanceOf(class: BlogPrompt::class)
            ->and(value: $blogPost->prompt->id)
            ->toBe(expected: $blogPrompt->id);
    });

    it('has a featured image relationship', function (): void
    {
        $blogPost  = BlogPost::factory()->create();
        $blogImage = BlogImage::factory()->create(attributes: [
            'post_id' => $blogPost->id,
            'type'    => BlogImageTypeEnum::FEATURED,
        ]);

        expect(value: $blogPost->featured())
            ->toBeInstanceOf(class: HasOne::class)
            ->and(value: $blogPost->featured)
            ->toBeInstanceOf(class: BlogImage::class)
            ->and(value: $blogPost->featured->id)
            ->toBe(expected: $blogImage->id)
            ->and(value: $blogPost->featured->type)
            ->toBe(expected: BlogImageTypeEnum::FEATURED);
    });

    it('has many images relationship', function (): void
    {
        $blogPost = BlogPost::factory()
            ->create();

        $blogImage1 = BlogImage::factory()->create(attributes: [
            'post_id' => $blogPost->id,
        ]);

        $blogImage2 = BlogImage::factory()->create(attributes: [
            'post_id' => $blogPost->id,
        ]);

        expect(value: $blogPost->images())
            ->toBeInstanceOf(class: HasMany::class)
            ->and(value: $blogPost->images)
            ->toBeInstanceOf(class: Collection::class)
            ->and(value: $blogPost->images->count())
            ->toBe(expected: 2)
            ->and(value: $blogPost->images->contains($blogImage1))
            ->toBeTrue()
            ->and(value: $blogPost->images->contains($blogImage2))
            ->toBeTrue();
    });

    it('generates a slug from the title with stop words removed', function (): void
    {
        $blogPost = BlogPost::factory()->create(attributes: [
            'title' => 'This is a Test Title',
            'slug'  => null,
        ]);

        expect(value: $blogPost->slug)
            ->toBe(expected: 'test-title');
    });

    it('returns slug options', function (): void
    {
        expect(value: (new BlogPost)->getSlugOptions())
            ->toBeInstanceOf(class: BlogPostSlugOptions::class);
    });

    it('returns slug as route key name', function (): void
    {
        expect(value: (new BlogPost)->getRouteKeyName())
            ->toBe(expected: 'slug');
    });

    it('generates featured image path', function (): void
    {
        $blogPost  = BlogPost::factory()->create();

        expect(value: $blogPost->featuredImagePath(file: 'test-image.jpg'))
            ->toContain(needles: $blogPost->id)
            ->toContain(needles: 'featured')
            ->toContain(needles: 'test-image.jpg');
    });

    it('generates temp image path', function (): void
    {
        $blogPost = BlogPost::factory()->create();
        $tempId   = Str::uuid()->toString();

        expect(value: $blogPost->tempImagePath(file: 'temp-image.jpg', id: $tempId))
            ->toContain(needles: $blogPost->id)
            ->toContain(needles: 'temp')
            ->toContain(needles: $tempId)
            ->toContain(needles: 'temp-image.jpg');
    });

    it('generates content image path', function (): void
    {
        $blogPost  = BlogPost::factory()->create();
        $contentId = Str::uuid()->toString();

        expect(value: $blogPost->contentImagePath(id: $contentId))
            ->toContain(needles: $blogPost->id)
            ->toContain(needles: 'content')
            ->toContain(needles: $contentId);
    });

    it('resolves route binding for published posts only', function (): void
    {
        $publishedPost = BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::PUBLISHED,
            'slug'   => 'published-post',
        ]);

        BlogPost::factory()->create(attributes: [
            'status' => PostStatusEnum::DRAFT,
            'slug'   => 'draft-post',
        ]);

        $resolvedPublished = new BlogPost()
            ->resolveRouteBinding(value: 'published-post');

        $resolvedDraft = new BlogPost()
            ->resolveRouteBinding(value: 'draft-post');

        expect(value: $resolvedPublished)
            ->toBeInstanceOf(class: BlogPost::class)
            ->and(value: $resolvedPublished->id)
            ->toBe(expected: $publishedPost->id)
            ->and(value: $resolvedDraft)
            ->toBeNull();

        $resolvedById = new BlogPost()->resolveRouteBinding(
            value: $publishedPost->id,
            field: 'id'
        );

        expect(value: $resolvedById)
            ->toBeInstanceOf(class: BlogPost::class)
            ->and(value: $resolvedById->id)
            ->toBe(expected: $publishedPost->id);
    });

    it('provides markdown html attribute', function (): void
    {
        $blogPost = BlogPost::factory()->create(attributes: [
            'content' => '# Test Heading\n\nTest content',
        ]);

        $postDto = new PostContentDTO(
            toc    : [['content' => 'Test Heading', 'slug' => 'test-heading', 'level' => 1]],
            tocHtml: '<ul><li><a href="#test-heading">Test Heading</a></li></ul>',
            html   : '<h1 id="test-heading">Test Heading</h1>\n<p>Test content</p>'
        );

        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(fn ($key, $ttl, $callback) => $key === "blog:post:{$blogPost->id}"
                    && $ttl instanceof DateTimeInterface
                    && is_callable(value: $callback))->andReturn($postDto);

        expect(value: $blogPost->markdownHtml)
            ->toBeInstanceOf(class: PostContentDTO::class)
            ->and(value: $blogPost->markdownHtml->html)
            ->toContain(needles: '<h1 id="test-heading">Test Heading</h1>');
    });
});
