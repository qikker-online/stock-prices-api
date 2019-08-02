<?php

namespace QikkerOnline\StockPricesApi\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use QikkerOnline\StockPricesApi\Exceptions\ApiResponseError;
use QikkerOnline\StockPricesApi\Exceptions\HandlesResponseErrors;

class EodApi implements StockPricesApiDriver
{
    use HandlesResponseErrors;


    const BASE_URL = 'https://eodhistoricaldata.com/';
    /**
     * @var Client
     */
    private $guzzle;
    private $apiKey;

    /**
     * EodApi constructor.
     *
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->apiKey = config('stockpricesapi.eod_api_key');
    }


    /**
     * @param string $symbol
     *
     * @return mixed
     * @throws ApiResponseError
     */
    public function getPrice(string $symbol)
    {
        $url = self::BASE_URL . 'api/real-time/' . $symbol;

        Log::info('Calling EOD API on ' . $url);

        try {
            $response = $this->guzzle->get($url, [
                'query' => [
                    'api_token' => $this->apiKey,
                    'fmt'       => 'json',
                ]
            ]);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array $symbols
     *
     * @return mixed
     * @throws ApiResponseError
     */
    public function getBatch(array $symbols)
    {
        $symbol  = array_shift($symbols);
        $symbols = implode(',', $symbols);

        $url = self::BASE_URL . 'api/real-time/' . $symbol;

        Log::info('Calling EOD API on ' . $url . ' - symbols: ' . $symbols);

        try {
            $response = $this->guzzle->get($url, [
                'query' => [
                    'api_token' => $this->apiKey,
                    'fmt'       => 'json',
                    's'         => $symbols
                ]
            ]);
        } catch (RequestException $e) {
            $this->handleError($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $symbol
     *
     * @return string|null
     * @throws ApiResponseError
     */
    public function getClosePrice(string $symbol)
    {
        $data = $this->getPrice($symbol);

        return $data['close'] ?? null;
    }

    /**
     * @param array $symbols
     *
     * @return array
     * @throws ApiResponseError
     */
    public function getBatchClosePrice(array $symbols)
    {
        $data = $this->getBatch($symbols);

        return $this->parseMultiple($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function parseMultiple($data)
    {
        $final = [];

        $dataBuffer = $data;

        if (!is_array(array_values($dataBuffer)[0])) {
            $dataBuffer   = [];
            $dataBuffer[] = $data;
        }

        // Split the code to stock symbol and exchange
        // Also fall back to the previous closing price when the price is 'NA'
        foreach ($dataBuffer as $stock) {
            $buffer        = explode('.', $stock['code']);
            $previousClose = $stock['previousClose'] == 'NA' ? null : $stock['previousClose'];
            $final[]       = [
                'symbol'   => $buffer[0],
                'exchange' => isset($buffer[1]) ? $buffer[1] : null,
                'close'    => $stock['close'] == 'NA' ? $previousClose : $stock['close']
            ];
        }

        return $final;
    }
}