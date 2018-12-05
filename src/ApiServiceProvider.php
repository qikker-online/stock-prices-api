<?php

namespace QikkerOnline\StockPricesApi;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{


    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/stockpricesapi.php' => config_path('stockpricesapi.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/stockpricesapi.php', 'stockpricesapi');

    }


    public function register()
    {
        $this->app->bind(StockPriceApi::class);
    }
}