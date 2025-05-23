<?php

declare(strict_types=1);

namespace App\Enums;

enum PostStatusEnum: int
{
    case SCHEDULED     = 1;
    case PUBLISHED     = 2;
    case DRAFT         = 3;
    case ARCHIVED      = 4;
    case PENDING_IMAGE = 5;
}
