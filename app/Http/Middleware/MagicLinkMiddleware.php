<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Session;
use Override;

final class MagicLinkMiddleware extends ValidateSignature
{
    /**
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @param  string[]  ...$args
     */
    #[Override]
    public function handle($request, Closure $next, ...$args): Response | RedirectResponse
    {
        try
        {
            return parent::handle($request, $next, ...$args);
        }
        catch (Exception)
        {
            Session::put('app-message', [
                'type'    => 'error',
                'message' => 'auth.sign.in.link.expired',
            ]);

            return redirect()->route(route: 'home');
        }
    }
}
