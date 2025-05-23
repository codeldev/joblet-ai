<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogIdea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/** @extends Factory<BlogIdea> */
final class BlogIdeaFactory extends Factory
{
    /** @var class-string<BlogIdea> */
    protected $model = BlogIdea::class;

    /**
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'topic'         => fake()->sentence(nbWords: random_int(5, 10)),
            'keywords'      => fake()->words(nb: random_int(3, 6), asText: true),
            'focus'         => fake()->paragraph(nbSentences: random_int(2, 4)),
            'requirements'  => fake()->paragraphs(nb: random_int(2, 4), asText: true),
            'additional'    => fake()->paragraphs(nb: random_int(2, 3), asText: true),
            'schedule_date' => fake()->dateTimeBetween(startDate: 'now', endDate: '+3 months'),
        ];
    }
}
