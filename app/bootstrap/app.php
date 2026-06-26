<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ExtendTokenExpiration;
use App\Http\Middleware\SecurityHeaders;
use App\Presentation\Console\Commands\PublishScheduledCarsCommand;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        PublishScheduledCarsCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
        $middleware->appendToGroup('api', [
            'throttle:api',
            ExtendTokenExpiration::class,
        ]);
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $_, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => '認証が必要です'], 401);
            }
        });
    })->create();
