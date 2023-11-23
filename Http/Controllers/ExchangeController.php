<?php

namespace App\Http\Controllers;

use App\Services\ExchangeService;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Exchange
 *
 * Endpoints for managing exchanges
 */
class ExchangeController extends Controller
{
    public ExchangeService $exchangeService;

    /**
     * @param ExchangeService $exchangeService
     */
    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * List
     *
     * Returns list of available exchanges
     * @authenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $currencyInfo = $this->exchangeService->getUserCurrenciesData();
//        $currencyInfoDataArray = $currencyInfo->getData(true);

        // Here we call method to save the response data about the currency in the currencies table
//        $this->exchangeService->saveExchangeRates($currencyInfoDataArray);

        return new JsonResponse($currencyInfo);
    }

    /**
     * Create
     *
     * Store a newly created exchange in storage.
     * @authenticated
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show
     *
     * Display the specified exchange.
     * @authenticated
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified exchange in storage.
     * @authenticated
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified exchange from storage.
     * @authenticated
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
