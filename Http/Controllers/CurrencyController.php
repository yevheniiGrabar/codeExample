<?php

namespace App\Http\Controllers;

use App\Http\Requests\Currency\StoreRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Currency
 *
 * Endpoints for managing currencies
 */
class CurrencyController extends Controller
{

    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private CurrencyService $currencyService;

    public function __construct(JsonResponseDataTransform $dataTransform, CurrencyService $currencyService)
    {
        $this->dataTransform = $dataTransform;
        $this->currencyService = $currencyService;
    }

    /**
     * List
     *
     * Returns list of available currencies
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // $currencies = $this->currencyService->getCurrencies();
        $currencies = Currency::all()->sortByDesc('id');

        return $this->dataTransform->conditionalResponse(
            $request,
            CurrencyResource::collection($currencies)
        );
    }

    /**
     * Create
     *
     * Store a newly created currency in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $currency = $this->currencyService->createCurrency($request->validated());

        return new JsonResponse(new CurrencyResource($currency->load('users')), Response::HTTP_CREATED);
    }

    /**
     * Delete
     *
     * Remove the specified currency from storage.
     * @authenticated
     * @param Currency $currency
     * @return JsonResponse
     */
    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();

        return new JsonResponse([]);
    }
}
