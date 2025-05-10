<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MessageTypeEnum;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Message> */
final class MessageFactory extends Factory
{
    /** @var class-string<Message> */
    protected $model = Message::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name'    => fake()->name(),
            'email'   => str(string: fake()->name())->slug()->toString() . '@gmail.com',
            'message' => fake()->paragraph(),
            'user_id' => fake()->boolean() ? Str::uuid()->toString() : null,
            'type'    => fake()->randomElement(array: MessageTypeEnum::cases()),
        ];
    }
}
