<?php

declare(strict_types=1);

use App\Enums\PageErrorEnum;

if (! function_exists(function: 'getErrorResponseTitle'))
{
    function getErrorResponseTitle(int $code): string
    {
        return PageErrorEnum::from(value: $code)->getTitle();
    }
}

if (! function_exists(function: 'getErrorResponseText'))
{
    function getErrorResponseText(int $code, ?string $message = null): string
    {
        return notEmpty(value: $message)
            ? (string) $message
            : PageErrorEnum::from(value: $code)->getDescription();
    }
}
