<?php

declare(strict_types=1);

namespace App\Enums;

enum LetterStyleEnum: int
{
    case CASUAL = 1;
    case FORMAL = 2;
    case PRO    = 3;

    public function text(): string
    {
        return match ($this)
        {
            self::CASUAL => trans(key: 'prompt.system.style.casual'),
            self::FORMAL => trans(key: 'prompt.system.style.formal'),
            self::PRO    => trans(key: 'prompt.system.style.professional'),
        };
    }
}
