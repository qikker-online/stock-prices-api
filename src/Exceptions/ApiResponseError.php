<?php

namespace QikkerOnline\StockPricesApi\Exceptions;

class ApiResponseError extends \Exception
{
    public static function badRequest($statusCode, $message)
    {
        return new static("The API responded with a Bad Request error, status code: {$statusCode}, message: '{$message}'");
    }

    public static function internalServerError($statusCode, $message)
    {
        return new static("The API responded with an Internal Server error, status code: {$statusCode}, message: '{$message}'");
    }
}