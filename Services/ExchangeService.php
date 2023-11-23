<?php

namespace App\Services;

use App\Models\Currency;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as StatusResponse;

class ExchangeService
{
    protected string $baseUrl;
    protected string $accessKey;
    protected string $baseCurrency;
    protected array $otherCurrencies = array();
    protected array $currenciesCodes = array();

    public function __construct()
    {
        $this->baseUrl = 'https://api.exchangerate.host';
        $this->accessKey = 'access_key=' . env('EXCHANGE_ACCESS_KEY');
//        $this->baseCurrency = 'NOK';
//        $this->otherCurrencies = ['USD', 'EUR', 'PLN'];
    }

    /**
     * @return JsonResponse
     */
    public function getLatestExchangeRates(): JsonResponse
    {
        $otherCurrenciesCommaSeparated = implode(',', $this->otherCurrencies);
        $requestUrl = "{$this->baseUrl}/latest?{$this->accessKey}&base={$this->baseCurrency}&symbols={$otherCurrenciesCommaSeparated}";
        $responseJson = file_get_contents($requestUrl);

        if(false !== $responseJson) {
            try {
                $response = json_decode($responseJson);
                if($response->success === true) {
                    return response()->json($response);
                }
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'ExchangeService exception'], StatusResponse::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['message' => 'ExchangeService false response'], StatusResponse::HTTP_BAD_REQUEST);
    }

    public function getUserCurrenciesData()
    {
        $currencies = Currency::query()->where('user_id', Auth::id())->get();

        foreach ($currencies as $currency) {
            $this->currenciesCodes[] = strtoupper($currency->code);
        }

        $this->baseCurrency = strtoupper($currencies->where('is_base_currency', '!=', 'false')->first()->code);
        $currencyCodesCommaSeparated = implode(',', $this->currenciesCodes);
        $requestUrl = "{$this->baseUrl}/latest?{$this->accessKey}&base={$this->baseCurrency}&symbols={$currencyCodesCommaSeparated}";

        $responseJson = file_get_contents($requestUrl);

        if(false !== $responseJson) {
            try {
                $response = json_decode($responseJson);
                if($response->success === true) {
                    return response()->json($response);
                }
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'ExchangeService exception'], StatusResponse::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['message' => 'ExchangeService false response'], StatusResponse::HTTP_BAD_REQUEST);
    }
}
