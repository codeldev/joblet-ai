<?php

declare(strict_types=1);

namespace App\Enums;

enum PageErrorEnum: int
{
    case E400 = 400;
    case E401 = 401;
    case E402 = 402;
    case E403 = 403;
    case E404 = 404;
    case E405 = 405;
    case E406 = 406;
    case E407 = 407;
    case E408 = 408;
    case E409 = 409;
    case E410 = 410;
    case E411 = 411;
    case E412 = 412;
    case E413 = 413;
    case E414 = 414;
    case E415 = 415;
    case E500 = 500;
    case E501 = 501;
    case E502 = 502;
    case E503 = 503;
    case E504 = 504;
    case E505 = 505;

    public function getTitle(): string
    {
        return match ($this)
        {
            self::E400 => trans(key: 'errors.400.title'),
            self::E401 => trans(key: 'errors.401.title'),
            self::E402 => trans(key: 'errors.402.title'),
            self::E403 => trans(key: 'errors.403.title'),
            self::E404 => trans(key: 'errors.404.title'),
            self::E405 => trans(key: 'errors.405.title'),
            self::E406 => trans(key: 'errors.406.title'),
            self::E407 => trans(key: 'errors.407.title'),
            self::E408 => trans(key: 'errors.408.title'),
            self::E409 => trans(key: 'errors.409.title'),
            self::E410 => trans(key: 'errors.410.title'),
            self::E411 => trans(key: 'errors.411.title'),
            self::E412 => trans(key: 'errors.412.title'),
            self::E413 => trans(key: 'errors.413.title'),
            self::E414 => trans(key: 'errors.414.title'),
            self::E415 => trans(key: 'errors.415.title'),
            self::E500 => trans(key: 'errors.500.title'),
            self::E501 => trans(key: 'errors.501.title'),
            self::E502 => trans(key: 'errors.502.title'),
            self::E503 => trans(key: 'errors.503.title'),
            self::E504 => trans(key: 'errors.504.title'),
            self::E505 => trans(key: 'errors.505.title'),
        };
    }

    public function getDescription(): string
    {
        return match ($this)
        {
            self::E400 => trans(key: 'errors.400.message'),
            self::E401 => trans(key: 'errors.401.message'),
            self::E402 => trans(key: 'errors.402.message'),
            self::E403 => trans(key: 'errors.403.message'),
            self::E404 => trans(key: 'errors.404.message'),
            self::E405 => trans(key: 'errors.405.message'),
            self::E406 => trans(key: 'errors.406.message'),
            self::E407 => trans(key: 'errors.407.message'),
            self::E408 => trans(key: 'errors.408.message'),
            self::E409 => trans(key: 'errors.409.message'),
            self::E410 => trans(key: 'errors.410.message'),
            self::E411 => trans(key: 'errors.411.message'),
            self::E412 => trans(key: 'errors.412.message'),
            self::E413 => trans(key: 'errors.413.message'),
            self::E414 => trans(key: 'errors.414.message'),
            self::E415 => trans(key: 'errors.415.message'),
            self::E500 => trans(key: 'errors.500.message'),
            self::E501 => trans(key: 'errors.501.message'),
            self::E502 => trans(key: 'errors.502.message'),
            self::E503 => trans(key: 'errors.503.message'),
            self::E504 => trans(key: 'errors.504.message'),
            self::E505 => trans(key: 'errors.505.message'),
        };
    }
}
