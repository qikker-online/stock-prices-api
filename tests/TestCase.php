<?php

namespace QikkerOnline\StockPricesApi\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use QikkerOnline\StockPricesApi\ApiServiceProvider;
use QikkerOnline\StockPricesApi\StockPricesApiFacade;


abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ApiServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'StockPricesApi' => StockPricesApiFacade::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Change the config here if needed.
        //
        // The default api driver is eodhistoricaldata.com and the tests check
        // if this api is correctly called.
        //
        // $app['config']->set('stockpricesapi.api', 'for-example');
        $app['config']->set('stockpricesapi.eod_api_key', 'for-example');
    }

}