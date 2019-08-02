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
    }


    public function register()
    {
        $this->app->singleton('stockPricesApi', function () {
            return (resolve(StockPricesApiManager::class))->getApi();
        });
    }
}
