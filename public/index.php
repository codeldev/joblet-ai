<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(as_float: true));

if (file_exists(filename: $maintenance = __DIR__ . '/../storage/framework/maintenance.php'))
{
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(request: Request::capture());
