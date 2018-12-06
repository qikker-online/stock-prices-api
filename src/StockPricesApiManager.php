<?php
/**
 * Created by PhpStorm.
 * User: marijn
 * Date: 2018-12-05
 * Time: 16:44
 */

namespace QikkerOnline\StockPricesApi;


use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use QikkerOnline\StockPricesApi\Drivers\StockPricesApiDriver;
use QikkerOnline\StockPricesApi\Drivers\EodApi;

class StockPricesApiManager
{
    /**
     * @var Repository
     */
    private $config;
    private $api;


    /**
     * StockPricesApi constructor.
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->api = $this->getApi();

    }


    /**
     * Resolves the current driver
     *
     * @throws \Exception
     */
    public function getApi(): StockPricesApiDriver
    {
        switch ($this->config->get('stockpricesapi.api', 'eod')) {
            case 'eod':
                return resolve(EodApi::class);
                break;

            default:
                throw new \Exception("Not a valid api driver selected");
        }
    }





}