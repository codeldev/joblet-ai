<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('web-app')->group(callback: function (): void
{
    Route::get('/', App\Livewire\Home\Index::class)
        ->name('home');

    Route::get('terms', App\Livewire\Terms\Index::class)
        ->name('terms');

    Route::get('privacy', App\Livewire\Privacy\Index::class)
        ->name('privacy');

    Route::get('support', App\Livewire\Support\Index::class)
        ->name('support');

    Route::get('generator', App\Livewire\Generator\Index::class)
        ->name('generator');

    Route::middleware('guest')->group(function (): void
    {
        Route::get('auth', App\Livewire\Auth\Index::class)
            ->name('auth');

        Route::get('auth/{id}/{hash}', App\Http\Requests\Auth\MagicLoginLinkRequest::class)
            ->middleware('magicLink')
            ->name('magic');
    });

    Route::middleware('auth')->group(function (): void
    {
        Route::get('dashboard', App\Livewire\Dashboard\Index::class)
            ->name('dashboard');

        Route::get('account', App\Livewire\Account\Index::class)
            ->name('account');

        Route::name('payment.')
            ->prefix('payment/{gateway}')
            ->group(function (): void
            {
                Route::get('success', App\Http\Requests\Payment\PaymentSuccessRequest::class)
                    ->name('success');

                Route::get('cancel', App\Http\Requests\Payment\PaymentCancelledRequest::class)
                    ->name('cancel');

                Route::get('{package}', App\Http\Requests\Payment\PaymentProcessRequest::class)
                    ->name('request');
            });
    });
});

Route::get('sitemap', App\Http\Requests\Sitemap\SitemapRequest::class)->name('sitemap');

Route::name('webhooks.')
    ->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->prefix('webhooks')
    ->group(function (): void
    {
        Route::post('stripe', App\Http\Requests\Webhooks\StripeWebhookRequest::class)
            ->name('stripe');
    });

if (app()->isLocal())
{
    include_once base_path(path: 'routes/dev.php');
}
