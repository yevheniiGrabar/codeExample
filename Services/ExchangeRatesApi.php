<?php

namespace App\Services;

use App\Models\Currency;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRatesApi
{
    protected $client;
    protected $baseUrl;
    protected $accessKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://api.exchangeratesapi.io';
//        $this->accessKey = config('services.exchangeratesapi.access_key');
//        $this->accessKey = 'GbE7VWtVKCvlADf1J4HM0fS45mVUrsRl'; //refactor this
        $this->accessKey = 'GbE7VWtVKCvlADf1J4HM0fS45mVUrsRl';
    }

    /**
     * @param string $base
     * @return mixed
     * @throws GuzzleException
     */
    public function getLatestRates($base = 'USD'): mixed
    {
        $url = "{$this->baseUrl}/latest?access_key={$this->accessKey}&base={$base}";

        $response = $this->client->get($url);

        return json_decode($response->getBody(), true);
    }

    /**
     * @param $date
     * @param string $base
     * @return mixed
     * @throws GuzzleException
     */
    public function getHistoricalRates($date, $base = 'USD'): mixed
    {
        $url = "{$this->baseUrl}/{$date}?access_key={$this->accessKey}&base={$base}";

        $response = $this->client->get($url);

        return json_decode($response->getBody(), true);
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getCurrencyList(): array
    {
        $client = new Client();
        $url = 'https://api.exchangeratesapi.io/latest';

        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);

        return array_keys($data['rates']);
    }

    /**
     * @throws GuzzleException
     */
    public function saveCurrencyList()
    {
        $client = new Client();
        $url = 'https://api.exchangeratesapi.io/latest';

        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);

        $currencies = [];
        foreach ($data['rates'] as $code => $rate) {
            $currencies[] = [
                'name' => Currency::getNameByCode($code), // получаем название валюты по коду
                'code' => $code,
            ];
        }

        // сохраняем список валют в базу данных
        Currency::upsert($currencies, ['code'], ['name']);
    }

}
