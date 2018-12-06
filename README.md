# Stock Prices API

## Installation

1. `composer require ...`
2. `php artisan vendor:publish --provider="QikkerOnline\StockPricesApi\ApiServiceProvider"`
3. Change the settings in the `stockpricesapi.php` config file.


## Usage


Get the closing price of a stock:
```php
\StockPricesApi::getClosePrice($symbol);
```


Get the closing price of a batch of stocks:
```php
\StockPricesApi::getBatchClosePrice(array $arrayOfSymbols);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.