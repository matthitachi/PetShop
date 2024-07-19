<?php

use App\Http\Middleware\FormatResponse;
use App\Http\Middleware\JWT;
use App\Http\Middleware\Role;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt' => JWT::class,
            'role' => Role::class,
        ]);
        $middleware->append(FormatResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Exception $e) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $error = $e->getMessage();
            $trace = config('app.debug') ? $e->getTrace() : [];

            return Response::formatted(0, [], $status, $error, [], $trace);
        });
    })->create();
