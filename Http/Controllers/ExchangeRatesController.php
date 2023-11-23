<?php

namespace App\Http\Controllers;

use App\Services\ExchangeRatesApi;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Exchange rates
 *
 * Endpoints for managing exchange rates
 */
class ExchangeRatesController extends Controller
{
    protected ExchangeRatesApi $exchangeRatesApi;

    public function __construct(ExchangeRatesApi $exchangeRatesApi)
    {
        $this->exchangeRatesApi = $exchangeRatesApi;
    }

    /**
     * List
     *
     * Returns list of available exchange rates
     * @authenticated
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function index(): JsonResponse
    {
        $rates = $this->exchangeRatesApi->getLatestRates();

        return new JsonResponse(['payload' => $rates]);
    }
}
