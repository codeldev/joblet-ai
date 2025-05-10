<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configureUrls();
        $this->configureVite();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            prohibit: $this->app->isProduction()
        );
    }

    private function configureDates(): void
    {
        Date::use(handler: CarbonImmutable::class);
    }

    private function configureModels(): void
    {
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict(shouldBeStrict: ! $this->app->isProduction());
    }

    private function configureUrls(): void
    {
        URL::forceScheme(scheme: 'https');
    }

    private function configureVite(): void
    {
        Vite::useBuildDirectory(path: '');
    }
}
