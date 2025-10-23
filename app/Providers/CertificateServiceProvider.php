<?php

namespace App\Providers;

use App\Services\CertificateService;
use Illuminate\Support\ServiceProvider;

class CertificateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CertificateService::class, function ($app) {
            return new CertificateService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}