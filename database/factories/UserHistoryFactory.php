<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserHistory> */
final class UserHistoryFactory extends Factory
{
    /** @var class-string<UserHistory> */
    protected $model = UserHistory::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'email' => str(string: fake()->name())->slug()->toString() . '@gmail.com',
        ];
    }
}
