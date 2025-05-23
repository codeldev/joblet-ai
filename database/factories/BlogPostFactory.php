<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PostStatusEnum;
use App\Models\BlogIdea;
use App\Models\BlogPost;
use App\Models\BlogPrompt;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BlogPost> */
final class BlogPostFactory extends Factory
{
    /** @var class-string<BlogPost> */
    protected $model = BlogPost::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'idea_id'            => BlogIdea::factory(),
            'prompt_id'          => BlogPrompt::factory(),
            'title'              => fake()->sentence(),
            'slug'               => fake()->slug(),
            'description'        => fake()->text(),
            'summary'            => fake()->paragraph(nbSentences: 4),
            'content'            => fake()->paragraphs(nb: 10, asText: true),
            'status'             => fake()->randomElement(array: PostStatusEnum::cases()),
            'published_at'       => fake()->dateTime(),
            'scheduled_at'       => fake()->dateTime(),
            'has_featured_image' => fake()->boolean(),
            'word_count'         => fake()->numberBetween(int1: 2000, int2: 3500),
            'read_time'          => fake()->numberBetween(int1: 8, int2: 25),
        ];
    }
}
