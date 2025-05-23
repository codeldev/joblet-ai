<?php

declare(strict_types=1);

namespace App\Enums;

enum BlogImageTypeEnum: int
{
    case FEATURED = 1;
    case CONTENT  = 2;
}
