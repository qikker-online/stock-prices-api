<?php

namespace QikkerOnline\StockPricesApi\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

trait HandlesResponseErrors
{
    /**
     * @param RequestException $e
     */
    private function handlerror(RequestException $e)
    {
        if ($e instanceof ClientException) {
            throw ApiResponseError::badRequest($e->getResponse()->getStatusCode(), $e->getResponse()->getReasonPhrase());
        } elseif ($e instanceof ServerException) {
            throw ApiResponseError::internalServerError($e->getResponse()->getStatusCode(), $e->getResponse()->getReasonPhrase());
        } else {
            throw $e;
        }
    }
}