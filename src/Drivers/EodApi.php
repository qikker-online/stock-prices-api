<?php

namespace QikkerOnline\StockPricesApi\Drivers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class EodApi implements StockPricesApiDriver
{

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
        } catch (ClientException $e) {

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

        try {
            $response = $this->guzzle->get('/api/real-time/' . $symbol, [
                'query' => [
                    'api_token' => $this->apiKey,
                    'fmt'       => 'json',
                    's'         => $symbols
                ]
            ]);
        } catch (ClientException $e) {

        }

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

    public function getBatchClosePrice(array $symbols)
    {
        $data = $this->getBatch($symbols);

        return $this->parseMultiple($data);
    }

    private function parseMultiple($data)
    {
        $final = [];

        $dataBuffer = $data;
        if (!is_array(array_values($dataBuffer)[0])) {
            $dataBuffer = [];
            $dataBuffer[] = $data;
        }

        foreach ($dataBuffer as $stock) {
            $buffer = explode('.', $stock['code']);
            $previousClose = $stock['previousClose'] == 'NA' ? null : $stock['previousClose'];
            $final[] = [
                'symbol' => $buffer[0],
                'exchange' => isset($buffer[1]) ? $buffer[1] : null,
                'close' => $stock['close'] == 'NA' ? $previousClose : $stock['close']
            ];
        }

        return $final;
    }
}