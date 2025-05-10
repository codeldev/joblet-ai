<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use function Pest\Stressless\stress;

it('has a fast response time', function (): void
{
    $result = stress(url: 'joblet.test')
        ->concurrently(requests: 2);

    expect(value: $result->requests()
        ->duration()
        ->med())
        ->toBeLessThan(expected: 100);
});
