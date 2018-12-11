<?php

namespace QikkerOnline\StockPricesApi\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->apiKey = config('stockpricesapi.eod_api_key');
    }


    /**
     * @param string $symbol
     * @return mixed
     */
    public function getPrice(string $symbol)
    {
        try {
            $response = $this->guzzle->get(self::BASE_URL . 'api/real-time/' . $symbol, [
                'query' => [
                    'api_token' => $this->apiKey,
                    'fmt'       => 'json',
                ]
            ]);
        } catch (RequestException $e) {
            $this->handlerror($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array $symbols
     * @return mixed
     */
    public function getBatch(array $symbols)
    {
        $symbol  = array_shift($symbols);
        $symbols = implode(',', $symbols);


        $response = $this->guzzle->get(self::BASE_URL . 'api/real-time/' . $symbol, [
            'query' => [
                'api_token' => $this->apiKey,
                'fmt'       => 'json',
                's'         => $symbols
            ]
        ]);


        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $symbol
     * @return string|null
     */
    public function getClosePrice(string $symbol)
    {
        $data = $this->getPrice($symbol);

        return $data['close'] ?? null;
    }

    /**
     * @param array $symbols
     * @return array
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