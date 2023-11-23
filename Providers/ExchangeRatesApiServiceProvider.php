<?php

namespace App\Providers;

use App\Services\ExchangeRatesApi;
use Illuminate\Support\ServiceProvider;

class ExchangeRatesApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExchangeRatesApi::class, function ($app) {
            return new ExchangeRatesApi();
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
