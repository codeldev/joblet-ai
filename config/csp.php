<?php

declare(strict_types=1);

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;

return [
    'presets' => [
        Spatie\Csp\Presets\Basic::class,
        Spatie\Csp\Presets\BunnyFonts::class,
        Spatie\Csp\Presets\Fathom::class,
    ],
    'directives' => [
        [Directive::STYLE, Keyword::UNSAFE_INLINE],
        [Directive::SCRIPT, Keyword::UNSAFE_INLINE],
        [Directive::SCRIPT, Keyword::UNSAFE_EVAL],
    ],
    'report_only_presets'         => [],
    'report_only_directives'      => [],
    'report_uri'                  => env(key: 'CSP_REPORT_URI', default: ''),
    'enabled'                     => env(key: 'CSP_ENABLED', default: true),
    'nonce_enabled'               => env(key: 'CSP_NONCE_ENABLED', default: false),
];
