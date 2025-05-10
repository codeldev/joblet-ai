<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\Backups\BackupServiceInterface;
use App\Contracts\Services\Backups\CleanupServiceInterface;
use App\Services\Backups\BackupService;
use App\Services\Backups\CleanupService;
use Illuminate\Support\ServiceProvider;
use Override;

final class BackupServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: BackupServiceInterface::class,
            concrete: BackupService::class
        );

        $this->app->bind(
            abstract: CleanupServiceInterface::class,
            concrete: CleanupService::class
        );
    }
}
