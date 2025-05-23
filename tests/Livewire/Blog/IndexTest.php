<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\BlogImageTypeEnum;
use App\Enums\PostStatusEnum;
use App\Livewire\Blog\Index;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Livewire\Livewire;

test(description: 'component can render', closure: function (): void
{
    Livewire::test(name: Index::class)
        ->assertStatus(status: 200);
});

test(description: 'component displays published posts', closure: function (): void
{
    $publishedPosts = BlogPost::factory(count: 3)->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'has_featured_image' => true,
    ]);

    foreach ($publishedPosts as $post)
    {
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
    }

    BlogPost::factory(count: 2)->create(attributes: [
        'status' => PostStatusEnum::DRAFT,
    ]);

    Livewire::test(name: Index::class)->assertViewHas(
        key: 'posts',
        value: function ($posts) use ($publishedPosts)
        {
            $thesePosts = $posts->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $published  = $publishedPosts->pluck(value: 'id')
                ->sort()
                ->values()
                ->toArray();

            return $posts->count() === 3 && $thesePosts === $published;
        }
    );
});

test(description: 'component orders posts by published date desc', closure: function (): void
{
    $oldPost = BlogPost::factory()->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'published_at'       => now()->subDays(value: 5),
        'has_featured_image' => true,
    ]);

    $newPost = BlogPost::factory()->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'published_at'       => now()->subDay(),
        'has_featured_image' => true,
    ]);

    $newestPost = BlogPost::factory()->create(attributes: [
        'status'             => PostStatusEnum::PUBLISHED,
        'published_at'       => now(),
        'has_featured_image' => true,
    ]);

    foreach ([$oldPost, $newPost, $newestPost] as $post)
    {
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
    }

    $postIds = [
        $newestPost->id,
        $newPost->id,
        $oldPost->id,
    ];

    Livewire::test(name: Index::class)->assertViewHas(
        key: 'posts',
        value: fn ($posts) => $posts->count() === 3 && $posts->pluck('id')->toArray() === $postIds
    );
});

test(description: 'component provides storage disk to view', closure: function (): void
{
    Livewire::test(name: Index::class)
        ->assertViewHas(key: 'disk');
});
