<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<User> */
final class UserFactory extends Factory
{
    /** @var class-string<User> */
    protected $model = User::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name'           => fake()->name(),
            'email'          => str(string: fake()->name())->slug()->toString() . '@gmail.com',
            'password'       => bcrypt(value: 'password'),
            'remember_token' => Str::random(length: 10),
        ];
    }
}
