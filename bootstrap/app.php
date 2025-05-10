<?php

declare(strict_types=1);

use App\Contracts\Actions\System\SendExceptionMailActionInterface;
use App\Http\Middleware\MagicLinkMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web : __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health  : '/up',
    )
    ->withMiddleware(callback: function (Middleware $middleware): void
    {
        $middleware
            ->statefulApi()
            ->redirectUsersTo(
                redirect: fn (Request $request) => route(name: 'dashboard')
            )
            ->redirectGuestsTo(
                redirect: fn (Request $request) => route(name: 'auth')
            )
            ->appendToGroup(group: 'web-app', middleware: [
                Treblle\SecurityHeaders\Http\Middleware\RemoveHeaders::class,
                Treblle\SecurityHeaders\Http\Middleware\ContentTypeOptions::class,
                Treblle\SecurityHeaders\Http\Middleware\PermissionsPolicy::class,
                Treblle\SecurityHeaders\Http\Middleware\SetReferrerPolicy::class,
                Treblle\SecurityHeaders\Http\Middleware\StrictTransportSecurity::class,
                Treblle\SecurityHeaders\Http\Middleware\CertificateTransparencyPolicy::class,
                Spatie\Csp\AddCspHeaders::class,
            ])
            ->alias(aliases: [
                'magicLink' => MagicLinkMiddleware::class,
            ]);
    })
    ->withExceptions(using: function (Exceptions $exceptions): void
    {
        $exceptions->reportable(reportUsing: function (Throwable | Exception | JsonException $e): void
        {
            /** @var SendExceptionMailActionInterface $action */
            $action = app()->make(
                abstract  : SendExceptionMailActionInterface::class,
                parameters: ['exception' => $e]
            );

            $action->handle();
        });
    })
    ->create();
