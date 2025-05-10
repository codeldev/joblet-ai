<?php

declare(strict_types=1);

namespace App\Enums;

enum MessageTypeEnum: int
{
    case CONTACT  = 1;
    case FEEDBACK = 2;
}
