<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\Invoice\InvoiceDataServiceInterface;
use App\Contracts\Services\Invoice\InvoiceServiceInterface;
use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Services\Invoice\InvoiceDataService;
use App\Services\Invoice\InvoiceService;
use App\Services\Invoice\PdfService;
use Illuminate\Support\ServiceProvider;
use Override;

final class InvoiceServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: InvoiceDataServiceInterface::class,
            concrete: InvoiceDataService::class
        );

        $this->app->bind(
            abstract: PdfServiceInterface::class,
            concrete: PdfService::class
        );

        $this->app->bind(
            abstract: InvoiceServiceInterface::class,
            concrete: InvoiceService::class
        );
    }
}
