<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Yaza\LaravelGoogleDriveStorage\Gdrive as GdriveAlias;

final class Gdrive extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GdriveAlias::class;
    }
}
