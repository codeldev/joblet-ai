<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\DbDumper\Databases\MySql;

final class MySqlDumper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MySql::class;
    }
}
