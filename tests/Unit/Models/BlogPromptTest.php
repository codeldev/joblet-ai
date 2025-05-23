<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\BlogPrompt;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasOne;

describe(description: 'BlogPrompt Model', tests: function (): void
{
    it('can be created using the factory', function (): void
    {
        $blogPrompt = BlogPrompt::factory()
            ->create();

        expect(value: $blogPrompt)
            ->toBeInstanceOf(class: BlogPrompt::class)
            ->and(value: $blogPrompt->id)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->meta_title)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->meta_description)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->post_content)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->post_summary)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->image_prompt)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->system_prompt)
            ->not->toBeEmpty()
            ->and(value: $blogPrompt->user_prompt)
            ->not->toBeEmpty();
    });

    it('has created_at and updated_at timestamps as CarbonImmutable instances', function (): void
    {
        $blogPrompt = BlogPrompt::factory()
            ->create();

        expect(value: $blogPrompt->created_at)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogPrompt->updated_at)
            ->toBeInstanceOf(class: CarbonImmutable::class);
    });

    it('has a blog post relationship', function (): void
    {
        $blogPrompt = BlogPrompt::factory()->create();
        $blogPost   = BlogPost::factory()->create(attributes: [
            'prompt_id' => $blogPrompt->id,
        ]);

        expect(value: $blogPrompt->post())
            ->toBeInstanceOf(class: HasOne::class)
            ->and(value: $blogPrompt->post)
            ->toBeInstanceOf(class: BlogPost::class)
            ->and(value: $blogPrompt->post->id)
            ->toBe(expected: $blogPost->id);
    });
});
