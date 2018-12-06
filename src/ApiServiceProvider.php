<?php

namespace QikkerOnline\StockPricesApi;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use QikkerOnline\StockPricesApi\Drivers\StockPricesApiDriver;

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
        $this->app->singleton(StockPricesApi::class);

//        $this->app->alias(StockPricesApiFacade::class, 'StockPricesApi');
    }
}