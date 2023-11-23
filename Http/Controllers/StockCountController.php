<?php

namespace App\Http\Controllers;

use App\Exports\StockCountExport;
use App\Http\Requests\StockCountStoreRequest;
use App\Http\Requests\StockCountUpdateRequest;
use App\Http\Resources\StockCountResource;
use App\Models\StockCount;
use App\Services\JsonResponseDataTransform;
use App\Services\StockCountService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Stock count
 *
 * Endpoints for managing stock counts
 */
class StockCountController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    protected JsonResponseDataTransform $dataTransform;

    /**
     * @var StockCountService
     */
    protected StockCountService $stockCountService;

    public function __construct(JsonResponseDataTransform $dataTransform, StockCountService $stockCountService)
    {
        $this->dataTransform = $dataTransform;
        $this->stockCountService = $stockCountService;
    }

    /**
     * List
     *
     * Display a listing of the stock count.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stockCounts = $this->stockCountService->getStockCounts($request);

        return new JsonResponse(['payload' => StockCountResource::collection($stockCounts)]);
    }

    /**
     * Create
     *
     * Store a newly created stock count in storage.
     * @authenticated
     * @param StockCountStoreRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StockCountStoreRequest $request): JsonResponse
    {
        $stockCount = $this->stockCountService->createStockCounts($request->validated());

        return new JsonResponse(['payload' => StockCountResource::make($stockCount)]);
    }

    /**
     * Show
     *
     * Display the specified stock count.
     * @authenticated
     * @param StockCount $stockCount
     * @return JsonResponse
     */
    public function show(StockCount $stockCount): JsonResponse
    {
        return new JsonResponse(['payload' => StockCountResource::make($stockCount)]);
    }

    /**
     * Edit
     *
     * Update the specified stock count in storage.
     * @authenticated
     * @param StockCountUpdateRequest $request
     * @param StockCount $stockCount
     * @return JsonResponse
     */
    public function update(StockCountUpdateRequest $request, StockCount $stockCount): JsonResponse
    {
        $stockCount = $this->stockCountService->updateStockCounts($request->validated(), $stockCount);

        return new JsonResponse(['payload' => StockCountResource::make($stockCount)]);
    }

    /**
     * Export
     *
     * Export all products from storage to csv(xslt).
     * @authenticated
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
    {
        $stockCounts = $this->stockCountService->exportStockCounts($request);

        return Excel::download(new StockCountExport($stockCounts), 'StockCountExport.xlsx');
    }

    public function merge(Request $request): JsonResponse
    {
        $stockCounts = $this->stockCountService->mergeStockCounts($request);

        return new JsonResponse(['payload' => StockCountResource::collection($stockCounts)]);
    }
}
