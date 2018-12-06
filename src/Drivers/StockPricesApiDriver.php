<?php

namespace QikkerOnline\StockPricesApi\Drivers;


interface StockPricesApiDriver
{

    public function getPrice(string $symbol);

    public function getBatch(array $symbols);

    /**
     * @param string $symbol
     * @return string|null
     */
    public function getClosePrice(string $symbol);

    public function getBatchClosePrice(array $symbols);
}