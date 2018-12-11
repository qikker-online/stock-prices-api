<?php

namespace QikkerOnline\StockPricesApi\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use QikkerOnline\StockPricesApi\Exceptions\ApiResponseError;

class ApiTest extends TestCase
{
    private $requestContainer;

    /**
     * http://docs.guzzlephp.org/en/stable/testing.html
     */
    private function setupGuzzleHandler(array $responses)
    {
        $this->requestContainer = [];
        $history                = Middleware::history($this->requestContainer);

        $mock = new MockHandler($responses);

        $stack = HandlerStack::create($mock);
        // Add the history middleware to the handler stack.
        $stack->push($history);

        return new Client(['handler' => $stack]);
    }

    /** @test */
    public function it_retrieves_the_price()
    {
        $responses = [
            new Response(200, [], '{"close": "2.01"}')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $this->assertEquals([
            'close' => 2.01
        ], \StockPricesApi::getPrice('test'));

        $this->assertEquals('/api/real-time/test', $this->requestContainer[0]['request']->getUri()->getPath());
        $this->assertEquals('api_token=for-example&fmt=json',
            $this->requestContainer[0]['request']->getUri()->getQuery());
    }


    /** @test */
    public function it_retrieves_multiple_prices()
    {
        $responses = [
            new Response(200, [], '[{"close": "2.01"},{"close": "4.23"}]')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $symbols = [
            'first',
            'second'
        ];

        $this->assertEquals([
            ["close" => 2.01],
            ["close" => 4.23]
        ], \StockPricesApi::getBatch($symbols));

        $this->assertEquals('/api/real-time/first', $this->requestContainer[0]['request']->getUri()->getPath());
        $this->assertEquals('api_token=for-example&fmt=json&s=second',
            $this->requestContainer[0]['request']->getUri()->getQuery());
    }

    /** @test */
    public function it_retrieves_the_close_price()
    {
        $responses = [
            new Response(200, [], '{"code":"APLE.US","timestamp":1543957413,"gmtoffset":0,"open":15.89,"high":15.92,"low":15.615,"close":15.66,"volume":1389569,"previousClose":15.95,"change":-0.29,"change_p":-0.018}')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $this->assertEquals(15.66, \StockPricesApi::getClosePrice('test'));

        $this->assertEquals('/api/real-time/test', $this->requestContainer[0]['request']->getUri()->getPath());
        $this->assertEquals('api_token=for-example&fmt=json',
            $this->requestContainer[0]['request']->getUri()->getQuery());
    }

    /** @test */
    public function it_retrieves_multiple_close_prices()
    {
        $responses = [
            new Response(200, [], '[{"code":"APLE.US","timestamp":1543957413,"gmtoffset":0,"open":15.89,"high":15.92,"low":15.615,"close":15.66,"volume":1389569,"previousClose":15.95,"change":-0.29,"change_p":-0.018},{"code":"AMAT.US","timestamp":1543881600,"gmtoffset":0,"open":38,"high":38.065,"low":35.153,"close":35.43,"volume":25534,"previousClose":35.43,"change":0,"change_p":0}]')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $symbols = [
            'first',
            'second'
        ];

        $this->assertEquals([
            [
                'close'    => 15.66,
                'symbol'   => 'APLE',
                'exchange' => 'US'
            ],
            [
                'symbol'   => 'AMAT',
                'exchange' => 'US',
                'close'    => 35.43
            ]

        ], \StockPricesApi::getBatchClosePrice($symbols));

        $this->assertEquals('/api/real-time/first', $this->requestContainer[0]['request']->getUri()->getPath());
        $this->assertEquals('api_token=for-example&fmt=json&s=second',
            $this->requestContainer[0]['request']->getUri()->getQuery());
    }


    /** @test */
    public function it_throws_an_api_response_error_on_a_400_response() {
        $responses = [
            new Response(400, [], '')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $this->expectException(ApiResponseError::class);
        $this->expectExceptionMessage("The API responded with a Bad Request error, status code: 400, message: 'Bad Request'");

        \StockPricesApi::getPrice('test');
    }

    /** @test */
    public function it_throws_an_api_response_error_on_a_500_response() {
        $responses = [
            new Response(500, [], '')
        ];

        $this->app->instance(Client::class, $this->setupGuzzleHandler($responses));

        $this->expectException(ApiResponseError::class);
        $this->expectExceptionMessage("The API responded with an Internal Server error, status code: 500, message: 'Internal Server Error'");

        \StockPricesApi::getPrice('test');
    }

}