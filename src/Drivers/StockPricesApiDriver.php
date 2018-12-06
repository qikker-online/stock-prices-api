<?php

namespace QikkerOnline\StockPricesApi\Drivers;


interface StockPricesApiDriver
{
    public function returnTrue():bool;

    public function getPrice(string $symbol);

    public function getBatch(array $symbols);

    public function getClosePrice(string $symbol);

    public function getBatchClosePrice(array $symbols);
}