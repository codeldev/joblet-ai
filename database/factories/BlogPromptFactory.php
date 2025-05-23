<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogPrompt;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BlogPrompt> */
final class BlogPromptFactory extends Factory
{
    /** @var class-string<BlogPrompt> */
    protected $model = BlogPrompt::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'meta_title'       => fake()->sentence(),
            'meta_description' => fake()->sentence(),
            'post_content'     => fake()->paragraphs(nb: 5, asText: true),
            'post_summary'     => fake()->paragraph(),
            'image_prompt'     => fake()->paragraph(),
            'system_prompt'    => fake()->paragraphs(nb: 5, asText: true),
            'user_prompt'      => fake()->paragraphs(nb: 5, asText: true),
            'content_images'   => [
                'image-1' => fake()->paragraph(),
                'image-2' => fake()->paragraph(),
                'image-3' => fake()->paragraph(),
            ],
        ];
    }
}
