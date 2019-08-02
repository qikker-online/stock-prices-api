<?php

namespace QikkerOnline\StockPricesApi;

use Exception;
use Illuminate\Config\Repository;
use QikkerOnline\StockPricesApi\Drivers\EodApi;
use QikkerOnline\StockPricesApi\Drivers\StockPricesApiDriver;

class StockPricesApiManager
{
    /**
     * @var Repository
     */
    private $config;
    private $api;


    /**
     * StockPricesApi constructor.
     *
     * @param Repository $config
     *
     * @throws Exception
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->api = $this->getApi();
    }


    /**
     * Resolves the current driver
     *
     * @throws Exception
     */
    public function getApi(): StockPricesApiDriver
    {
        switch ($this->config->get('stockpricesapi.api', 'eod')) {
            case 'eod':
                return resolve(EodApi::class);
                break;

            default:
                throw new Exception("Not a valid api driver selected");
        }
    }
}
