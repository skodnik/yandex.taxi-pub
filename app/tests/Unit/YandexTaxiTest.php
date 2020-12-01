<?php

namespace Unit;

use App\YandexTaxi;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

final class YandexTaxiTest extends TestCase
{
    protected $yandex_taxi;
    protected $http;

    protected function setUp(): void
    {
        (Dotenv::createImmutable(__DIR__ . '/../../'))->load();
        $this->yandex_taxi = new YandexTaxi('to_home');
        $this->http = new Client();
    }

    protected function tearDown(): void
    {
    }

    public
    function testYandexTaxiSiteResponseOk()
    {
        $response = $this->http->get(env('YANDEX_TAXI_URI'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public
    function testGetJsonPayload()
    {
        $this->assertJson($this->yandex_taxi->getJsonPayload());
    }

    public
    function testYandexTaxiApiResponseCorrectJson()
    {
        $this->assertJson($this->yandex_taxi->getJsonDataFromYandexTaxi());
    }
}