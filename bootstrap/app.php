<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Configure serverless environment overrides dynamically (e.g. Vercel)
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') == true) {
    putenv('VIEW_COMPILED_PATH=/tmp');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp';

    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';

    if (empty(env('SESSION_DRIVER'))) {
        putenv('SESSION_DRIVER=cookie');
        $_ENV['SESSION_DRIVER'] = 'cookie';
        $_SERVER['SESSION_DRIVER'] = 'cookie';
    }

    if (empty(env('CACHE_STORE'))) {
        putenv('CACHE_STORE=database');
        $_ENV['CACHE_STORE'] = 'database';
        $_SERVER['CACHE_STORE'] = 'database';
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/cashfree',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.dashboard');
            }

            return route('account.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
