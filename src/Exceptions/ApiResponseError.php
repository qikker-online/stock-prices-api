<?php

namespace QikkerOnline\StockPricesApi\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ApiResponseError extends Exception
{
    /**
     * @param ClientException $e
     *
     * @return ApiResponseError
     */
    public static function badRequest(ClientException $e)
    {
        return new static(
            'The API responded with a Bad Request error, status code: ' . $e->getResponse()->getStatusCode() .
            ', message: ' . $e->getResponse()->getBody(),
            null,
            $e
        );
    }

    /**
     * @param ServerException $e
     *
     * @return ApiResponseError
     */
    public static function internalServerError(ServerException $e)
    {
        return new static(
            'The API responded with an Internal Server error, status code: ' . $e->getResponse()->getStatusCode() .
            ', message: ' . $e->getResponse()->getBody(),
            null,
            $e
        );
    }
}