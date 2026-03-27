<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiRateLimiter;
use App\Http\Middleware\AuthenticateApiKey;
use App\Http\Middleware\CheckApiScope;
use App\Http\Middleware\SetCoopHeader;
use App\Http\Middleware\UserOnlyMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        // $middleware->prepend(\App\Http\Middleware\ForceHttps::class);
        $middleware->prepend(SetCoopHeader::class);
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \App\Http\Middleware\VerifyCsrfToken::class);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckSuspended::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\CheckSuspended::class,
        ]);

        $middleware->alias([
            'api.auth' => \App\Http\Middleware\AuthenticateApiKey::class,
            'api.rate' => \App\Http\Middleware\ApiRateLimiter::class,
            'api.scope' => \App\Http\Middleware\CheckApiScope::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user.only' => \App\Http\Middleware\UserOnlyMiddleware::class,
            'suspended' => \App\Http\Middleware\CheckSuspended::class,
            'jwt.auth' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken::class,
            'jwt.check' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Check::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn (Request $request) =>
            $request->user() && in_array($request->user()->role, ['admin', 'super_admin'])
                ? route('admin.dashboard')
                : route('dashboard')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

