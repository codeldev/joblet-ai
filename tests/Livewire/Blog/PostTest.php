<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Livewire\Blog\Post;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Livewire\Livewire;

test(description: 'component can render with a blog post', closure: function (): void
{
    $post = BlogPost::factory()->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'has_featured_image' => true,
    ]);

    BlogImage::factory()->create(attributes: [
        'post_id' => $post->id,
        'type'    => BlogImageTypeEnum::FEATURED,
        'files'   => [
            ['width' => '400', 'image' => '400w.webp'],
            ['width' => '700', 'image' => '700w.webp'],
            ['width' => '1000', 'image' => '1000w.webp'],
            ['width' => '1920', 'image' => '1920w.webp'],
        ],
    ]);

    Livewire::test(
        name: Post::class,
        params: ['post' => $post]
    )->assertStatus(status: 200);
});

test(description: 'component renders with correct title', closure: function (): void
{
    $post = BlogPost::factory()->create(attributes: [
        'title'              => 'Test Blog Post Title',
        'status'             => PostStatusEnum::PUBLISHED,
        'has_featured_image' => true,
    ]);

    BlogImage::factory()->create(attributes: [
        'post_id' => $post->id,
        'type'    => BlogImageTypeEnum::FEATURED,
        'files'   => [
            ['width' => '400', 'image' => '400w.webp'],
            ['width' => '700', 'image' => '700w.webp'],
            ['width' => '1000', 'image' => '1000w.webp'],
            ['width' => '1920', 'image' => '1920w.webp'],
        ],
    ]);

    Livewire::test(
        name: Post::class,
        params: ['post' => $post]
    )->assertSee(values: 'Test Blog Post Title');
});

test(description: 'component provides correct storage disk to view', closure: function (): void
{
    $post = BlogPost::factory()->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'has_featured_image' => true,
    ]);

    BlogImage::factory()->create(attributes: [
        'post_id' => $post->id,
        'type'    => BlogImageTypeEnum::FEATURED,
        'files'   => [
            ['width' => '400', 'image' => '400w.webp'],
            ['width' => '700', 'image' => '700w.webp'],
            ['width' => '1000', 'image' => '1000w.webp'],
            ['width' => '1920', 'image' => '1920w.webp'],
        ],
    ]);

    Livewire::test(
        name  : Post::class,
        params: ['post' => $post]
    )->assertViewHas(
        key  : 'disk',
        value: StorageDiskEnum::BLOG_IMAGES->disk()
    );
});
