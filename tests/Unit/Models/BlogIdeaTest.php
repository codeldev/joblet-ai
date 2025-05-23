<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\BlogIdea;
use App\Models\BlogPost;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasOne;

describe(description: 'BlogIdea Model', tests: function (): void
{
    it('can be created using the factory', function (): void
    {
        $blogIdea = BlogIdea::factory()->create();

        expect(value: $blogIdea)
            ->toBeInstanceOf(class: BlogIdea::class)
            ->and(value: $blogIdea->id)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->topic)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->keywords)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->focus)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->requirements)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->additional)
            ->not->toBeEmpty()
            ->and(value: $blogIdea->schedule_date)
            ->toBeInstanceOf(class: CarbonImmutable::class);
    });

    it('casts dates to immutable datetime objects', function (): void
    {
        $blogIdea = BlogIdea::factory()->create(attributes: [
            'schedule_date' => now(),
            'queued_at'     => now(),
            'processed_at'  => now(),
        ]);

        expect(value: $blogIdea->schedule_date)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogIdea->queued_at)
            ->toBeInstanceOf(class: CarbonImmutable::class)
            ->and(value: $blogIdea->processed_at)
            ->toBeInstanceOf(class: CarbonImmutable::class);
    });

    it('has a post relationship', function (): void
    {
        $blogIdea = BlogIdea::factory()->create();

        expect(value: $blogIdea->post())
            ->toBeInstanceOf(class: HasOne::class)
            ->and(value: $blogIdea->post()->getRelated())
            ->toBeInstanceOf(class: BlogPost::class);
    });
});
