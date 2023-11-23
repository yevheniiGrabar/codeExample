<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReturnRequest;
use App\Http\Resources\SalesReturnResource;
use App\Models\SalesReturn;
use App\Models\Shipment;
use App\Services\JsonResponseDataTransform;
use App\Services\SalesReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Sale return
 *
 * Endpoints for managing sale returns
 */
class SalesReturnController extends Controller
{
    public JsonResponseDataTransform $dataTransform;
    public SalesReturnService $salesReturnService;

    public function __construct(JsonResponseDataTransform $dataTransform,
                                SalesReturnService $salesReturnService)
    {
        $this->dataTransform = $dataTransform;
        $this->salesReturnService = $salesReturnService;
    }

    /**
     * List
     *
     * Returns list of available sale returns
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $salesReturnData = $this->salesReturnService->getSalesReturnData();
        return $this->dataTransform->conditionalResponse($request, $salesReturnData);
    }

    /**
     * Create
     *
     * Store a newly created sale return in storage.
     * @authenticated
     * @param SalesReturnRequest $request
     * @return JsonResponse
     */
    public function store(SalesReturnRequest $request): JsonResponse
    {
        $salesReturn = SalesReturn::query()->create($request->validated());
        return new JsonResponse(new SalesReturnResource($salesReturn), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified sale return.
     * @authenticated
     * @param SalesReturn $salesReturn
     * @return JsonResponse
     */
    public function show(SalesReturn $salesReturn): JsonResponse
    {
        return new JsonResponse(['payload' => [SalesReturnResource::make($salesReturn)]], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified sale return in storage.
     * @authenticated
     * @param SalesReturnRequest $request
     * @param SalesReturn $salesReturn
     * @return JsonResponse
     */
    public function update(SalesReturnRequest $request, SalesReturn $salesReturn): JsonResponse
    {
        $salesReturn->update($request->validated());
        return new JsonResponse(new SalesReturnResource($salesReturn), Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified sale return from storage.
     * @authenticated
     * @param SalesReturn $salesReturn
     * @return JsonResponse
     */
    public function destroy(SalesReturn $salesReturn): JsonResponse
    {
        $salesReturn->delete();
        return new JsonResponse(['message' => 'Sales Return deleted successfully'], Response::HTTP_OK);
    }
}
