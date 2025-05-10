<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Models;

use App\Models\User;

/**
 * A mock implementation of User for testing
 */
final class MockUser
{
    public function __construct(public readonly string $id) {}

    public function __toString(): string
    {
        return $this->id;
    }
}
