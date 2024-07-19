<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\JWTService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(JWTService::class, function () {
            return new JWTService();
        });

        $this->app->bind(AuthService::class, function ($app) {
            return new AuthService($app->make(JWTService::class));
        });
    }
}
