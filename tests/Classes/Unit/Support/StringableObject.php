<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Support;

final class StringableObject
{
    public function __toString(): string
    {
        return 'object string';
    }
}
