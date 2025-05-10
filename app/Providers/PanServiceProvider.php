<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Override;
use Pan\PanConfiguration;

final class PanServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        /** @var string $attributes */
        $attributes = config(key: 'settings.pan');

        /** @var list<string> $allowable */
        $allowable = str(string: $attributes)
            ->explode(delimiter: '|')
            ->values()
            ->toArray();

        PanConfiguration::allowedAnalytics(
            names: $allowable
        );
    }
}
