<?php

namespace App\Providers;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Response::macro('formatted', function ($success, $data = [], $status = HttpResponse::HTTP_OK, $error = '', $errors = [], $trace = []) {
            return Response::json([
                'success' => $success,
                'data' => $data,
                'error' => $error,
                'errors' => $errors,
                //                'trace' => $trace,
            ], $status);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
