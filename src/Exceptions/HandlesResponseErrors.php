<?php

namespace QikkerOnline\StockPricesApi\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

trait HandlesResponseErrors
{
    /**
     * @param RequestException $e
     *
     * @throws ApiResponseError
     */
    private function handleError(RequestException $e)
    {
        if ($e instanceof ClientException) {
            throw ApiResponseError::badRequest($e);
        } elseif ($e instanceof ServerException) {
            throw ApiResponseError::internalServerError($e);
        }

        throw $e;
    }
}