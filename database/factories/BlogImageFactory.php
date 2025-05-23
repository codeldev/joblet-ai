<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BlogImageTypeEnum;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogImage> */
final class BlogImageFactory extends Factory
{
    /** @var class-string<BlogImage> */
    protected $model = BlogImage::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'post_id'     => BlogPost::factory(),
            'type'        => fake()->randomElement(array: BlogImageTypeEnum::cases()),
            'description' => fake()->text(),
            'files'       => [
                Str::random(length: 10) . 'jpg',
                Str::random(length: 10) . 'jpg',
                Str::random(length: 10) . 'jpg',
                Str::random(length: 10) . 'jpg',
            ],
        ];
    }
}
