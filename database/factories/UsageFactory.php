<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Generated;
use App\Models\Usage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Usage> */
final class UsageFactory extends Factory
{
    /** @var class-string<Usage> */
    protected $model = Usage::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'generated_id' => Generated::factory(),
            'word_count'   => fake()->numberBetween(int1: 200, int2: 500),
            'tokens_used'  => fake()->numberBetween(int1: 10000, int2: 2500),
        ];
    }
}
