<?php

namespace QikkerOnline\StockPricesApi;

use Illuminate\Support\Facades\Facade;

class StockPricesApiFacade extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'stockPricesApi';
    }
}