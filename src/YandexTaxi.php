<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YandexTaxi
{
    private $payload_array = [];
    private $response_yandex_taxi_json = '';
    private $response_yandex_taxi_json_clear = '';

    public $key_matching = [
        "econom" => 4,
        "business" => 5,
        "comfortplus" => 6,
        "vip" => 7,
        "ultimate" => 8,
        "maybach" => 9,
        "child_tariff" => 10,
        "minivan" => 11,
        "premium_van" => 12,
        "personal_driver" => 13,
        "express" => 14,
        "courier" => 15,
        "cargo" => 16,
    ];

    public function __construct($direction)
    {
        $route = ['from' => 1, 'to' => 2];

        if ($direction == 'to_home') {
            $route = ['from' => 2, 'to' => 1];
        }

        $this->payload_array = [
            'id' => makeId(),
            'zone_name' => '',
            'skip_estimated_waiting' => true,
            'supports_forced_surge' => true,
            'format_currency' => true,
            'extended_description' => true,
            'route' =>
                [
                    0 =>
                        [
                            0 => (float)env('COORDINATE_LONGITUDE_' . $route['from']),
                            1 => (float)env('COORDINATE_LATITUDE_' . $route['from']),
                        ],
                    1 =>
                        [
                            0 => (float)env('COORDINATE_LONGITUDE_' . $route['to']),
                            1 => (float)env('COORDINATE_LATITUDE_' . $route['to']),
                        ],
                ],
            'requirements' =>
                [
                    'nosmoking' => true,
                ],
        ];
    }

    public function go(string $direction)
    {
        $route = ['from' => 1, 'to' => 2];

        if ($direction == 'to_home') {
            $route = ['from' => 2, 'to' => 1];
        }

        $this->payload_array = [
            'id' => makeId(),
            'zone_name' => '',
            'skip_estimated_waiting' => true,
            'supports_forced_surge' => true,
            'format_currency' => true,
            'extended_description' => true,
            'route' =>
                [
                    0 =>
                        [
                            0 => (float)env('COORDINATE_LONGITUDE_' . $route['from']),
                            1 => (float)env('COORDINATE_LATITUDE_' . $route['from']),
                        ],
                    1 =>
                        [
                            0 => (float)env('COORDINATE_LONGITUDE_' . $route['to']),
                            1 => (float)env('COORDINATE_LATITUDE_' . $route['to']),
                        ],
                ],
            'requirements' =>
                [
                    'nosmoking' => true,
                ],
        ];

        $payload = $this->getJsonPayload();

        $http = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($payload)
                ]
            ]
        );

        try {
            $response = $http->post(env('YANDEX_TAXI_URI_API'), ['body' => $payload]);
        } catch (GuzzleException $e) {
            throw new \DomainException('yandex-taxi post error: ' . $e->getMessage());
        }

        $this->response_yandex_taxi_json = (string)$response->getBody();

        return $this;
    }

    public function getDataFromYandexTaxi()
    {
        $payload = $this->getJsonPayload();

        $http = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($payload)
                ]
            ]
        );

        try {
            $response = $http->post(env('YANDEX_TAXI_URI_API'), ['body' => $payload]);
        } catch (GuzzleException $e) {
            throw new \DomainException('yandex-taxi post error: ' . $e->getMessage());
        }

        $this->response_yandex_taxi_json = (string)$response->getBody();

        return $response;
    }

    public function getJsonDataFromYandexTaxi()
    {
        return (string)$this->getDataFromYandexTaxi()->getBody();
    }

    public function getArrayDataFromYandexTaxi()
    {
        $array = json_decode($this->getJsonDataFromYandexTaxi(), true);

        if (json_last_error() == JSON_ERROR_NONE) {
            return $array;
        } else {
            throw new \DomainException('YandexTaxi response json parse error');
        }
    }

    public function getPrepareArrayToExport(array $result_arr = null): array
    {
        $result_arr = $result_arr ?? $this->getArrayDataFromYandexTaxi();

        $data_to_export = [
            'date' => date('d.m.Y'),
            'time' => date('H:i:s'),
            'duration' => $result_arr['time_seconds'] / 60,
            'distance' => (int)$result_arr['distance'],
        ];

        $service_levels = $result_arr['service_levels'];

        foreach ($service_levels as $key => $service_level) {
            $prices[$service_level['class']] = (int)$service_level['price'];
        }

        foreach ($this->key_matching as $key => $index) {
            $data_to_export[$index] = $prices[$key] ?? 0;
        }

        return $data_to_export;
    }

    public function getDataToExport(array $result_arr = null): array
    {
        $result_arr = $result_arr ?? $this->getArrayDataFromYandexTaxi();

        $data_to_export = [
            'date' => date('d.m.Y'),
            'time' => date('H:i:s'),
            'duration' => $result_arr['time_seconds'] / 60,
            'distance' => (int)$result_arr['distance'],
        ];

        $service_levels = $result_arr['service_levels'];

        foreach ($service_levels as $key => $service_level) {
            $prices[$service_level['class']] = (int)$service_level['price'];
        }

        foreach ($this->key_matching as $key => $index) {
            $data_to_export[$key] = $prices[$key] ?? 0;
        }

        return $data_to_export;
    }

    public function getJsonPayload()
    {
        return json_encode($this->payload_array);
    }
}