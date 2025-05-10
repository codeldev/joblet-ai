<?php

declare(strict_types=1);

namespace App\Enums;

enum MaxTokensEnum: int
{
    case SHORT  = 1;
    case MEDIUM = 2;
    case LONG   = 3;

    public function tokens(): int
    {
        return match ($this)
        {
            self::SHORT  => 1250,
            self::MEDIUM => 1850,
            self::LONG   => 2500,
        };
    }
}
