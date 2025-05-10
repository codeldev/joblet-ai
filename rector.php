<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;

try
{
    return RectorConfig::configure()
        ->withPaths(paths: [
            __DIR__ . '/app',
            __DIR__ . '/bootstrap/app.php',
            __DIR__ . '/config',
            __DIR__ . '/database',
            __DIR__ . '/public',
        ])
        ->withPreparedSets(
            deadCode        : true,
            codeQuality     : true,
            typeDeclarations: true,
            privatization   : true,
            earlyReturn     : true,
            strictBooleans  : true,
        )
        ->withPhpSets(php84: true);
}
catch (InvalidConfigurationException $e)
{
    Log::error(message: $e->getMessage());
}
