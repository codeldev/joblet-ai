<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\Home\LetterServiceInterface;
use App\Services\Home\LetterService;
use Illuminate\Support\ServiceProvider;
use Override;

final class HomeServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: LetterServiceInterface::class,
            concrete: LetterService::class
        );
    }
}
