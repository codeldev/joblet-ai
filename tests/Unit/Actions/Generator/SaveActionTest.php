<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Generator\SaveAction;
use App\Models\Generated;

it('saves changes to a generated asset text', function (): void
{
    $text = fake()->text();

    (new SaveAction)->handle(
        asset   : Generated::factory()->create(),
        html    : "<p>{$text}</p>",
        callback: fn ($result) => expect(value: $result)->toBe(expected: $text)
    );
});
