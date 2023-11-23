<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceListStoreRequest;
use App\Http\Requests\PriceListUpdateRequest;
use App\Http\Resources\PriceListResource;
use App\Models\PriceList;
use App\Services\JsonResponseDataTransform;
use App\Services\PriceListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Price list
 *
 * Endpoints for managing price lists
 */
class PriceListController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private PriceListService $priceListService;

    public function __construct(JsonResponseDataTransform $dataTransform, PriceListService $priceListService)
    {
        $this->dataTransform = $dataTransform;
        $this->priceListService = $priceListService;
    }

    /**
     * List
     *
     * Returns list of available price lists
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            PriceListResource::collection(
                PriceList::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created price list in storage.
     * @authenticated
     * @param PriceListStoreRequest $request
     * @return JsonResponse
     */
    public function store(PriceListStoreRequest $request): JsonResponse
    {
        $priceList = $this->priceListService->createPriceList($request->validated());

        return new JsonResponse(new PriceListResource($priceList));
    }

    /**
     * Show
     *
     * Display the specified price list.
     * @authenticated
     * @param PriceList $priceList
     * @return JsonResponse
     */
    public function show(PriceList $priceList): JsonResponse
    {
        $priceList = PriceList::query()->find($priceList);

        return new JsonResponse(['payload' => PriceListResource::make($priceList)]);
    }

    /**
     * Edit
     *
     * Update the specified price list in storage.
     * @authenticated
     * @param PriceListUpdateRequest $request
     * @param PriceList $priceList
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PriceListUpdateRequest $request, PriceList $priceList): JsonResponse
    {
        $priceList = $this->priceListService->updatePriceList($priceList, $request->all());

        return new JsonResponse(new PriceListResource($priceList));
    }

    /**
     * Delete
     *
     * Remove the specified price list from storage.
     * @authenticated
     * @param PriceList $priceList
     * @return JsonResponse
     */
    public function destroy(PriceList $priceList): JsonResponse
    {
        $priceList->delete();

        return new JsonResponse(['message' => 'PriceList Deleted Successfully']);
    }
}
