<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\BlogImageTypeEnum;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

describe(description: 'BlogImage Model', tests: function (): void
{
    it('can be created using the factory', function (): void
    {
        $blogImage = BlogImage::factory()->create();

        expect(value: $blogImage)
            ->toBeInstanceOf(class: BlogImage::class)
            ->and(value: $blogImage->id)
            ->not->toBeEmpty()
            ->and(value: $blogImage->post_id)
            ->not->toBeEmpty()
            ->and(value: $blogImage->type)
            ->toBeInstanceOf(class: BlogImageTypeEnum::class)
            ->and(value: $blogImage->files)
            ->toBeInstanceOf(class: ArrayObject::class);
    });

    it('casts type to BlogImageTypeEnum', function (): void
    {
        $blogImage = BlogImage::factory()->create(attributes: [
            'type' => BlogImageTypeEnum::FEATURED,
        ]);

        expect(value: $blogImage->type)
            ->toBeInstanceOf(class: BlogImageTypeEnum::class)
            ->and(value: $blogImage->type)
            ->toBe(expected: BlogImageTypeEnum::FEATURED);
    });

    it('casts files to ArrayObject', function (): void
    {
        $files = [
            'image1.jpg',
            'image2.jpg',
        ];

        $blogImage = BlogImage::factory()->create(attributes: [
            'files' => $files,
        ]);

        expect(value: $blogImage->files)
            ->toBeInstanceOf(class: ArrayObject::class)
            ->and(value: $blogImage->files->count())
            ->toBe(expected: 2)
            ->and(value: $blogImage->files[0])
            ->toBe(expected: 'image1.jpg')
            ->and(value: $blogImage->files[1])
            ->toBe(expected: 'image2.jpg');
    });

    it('belongs to a blog post', function (): void
    {
        $blogPost  = BlogPost::factory()->create();
        $blogImage = BlogImage::factory()->create(attributes: [
            'post_id' => $blogPost->id,
        ]);

        expect(value: $blogImage->post())
            ->toBeInstanceOf(class: BelongsTo::class)
            ->and(value: $blogImage->post)
            ->toBeInstanceOf(class: BlogPost::class)
            ->and(value: $blogImage->post->id)
            ->toBe(expected: $blogPost->id);
    });
});
