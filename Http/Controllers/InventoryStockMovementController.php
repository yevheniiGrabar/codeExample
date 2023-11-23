<?php

namespace App\Http\Controllers;

use App\Exports\TransfersExport;
use App\Http\Requests\Transfer\ExportRequest;
use App\Http\Requests\Transfer\IndexRequest;
use App\Http\Requests\Transfer\ShowRequest;
use App\Http\Requests\Transfer\StoreRequest;
use App\Http\Resources\InventoryStockMovementResource;
use App\Models\InventoryStockMovement;
use App\Models\LocationProduct;
use App\Services\InventoryStockMovementRequestParser;
use App\Services\JsonResponseDataTransform;
use App\Services\StockTransferService;
use App\Traits\CurrentCompany;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Inventory stock movement
 *
 * Endpoints for managing inventory stock movements
 */
class InventoryStockMovementController extends Controller
{

    /** @var StockTransferService */
    public StockTransferService $stockTransferService;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    private InventoryStockMovementRequestParser $requestParser;

    private ConnectionInterface $connection;

    public function __construct(
        StockTransferService $stockTransferService,
        JsonResponseDataTransform $dataTransform,
        InventoryStockMovementRequestParser $requestParser,
        ConnectionInterface $connection
    ) {
        $this->stockTransferService = $stockTransferService;
        $this->dataTransform = $dataTransform;
        $this->requestParser = $requestParser;
        $this->connection = $connection;
    }

    /**
     * List
     *
     * Returns list of available inventory stock movements
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        $parsedRequest = $this->requestParser->parseRequest($request);

        $stockTransfers = InventoryStockMovement::filter(
            $parsedRequest['search'],
            $parsedRequest['products'],
            $parsedRequest['users'],
            $parsedRequest['location_from'],
            $parsedRequest['location_to'],
            $parsedRequest['remarks'],
            $parsedRequest['from'],
            $parsedRequest['to'],
        )->with(['product', 'user', 'locationFrom', 'sectionFrom', 'locationTo', 'sectionTo'])
            ->where('company_id', $defaultCompany->company_id)
            ->orderBy('id', 'desc')
            ->get();

        return $this->dataTransform->conditionalResponse(
            $request,
            InventoryStockMovementResource::collection($stockTransfers)
        );
    }


    /**
     * Create
     *
     * Store a newly created inventory stock movement in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $inventoryStockMovement = null;

        $this->connection->transaction(function () use ($request, &$inventoryStockMovement) {
            $currentCompany = CurrentCompany::getDefaultCompany();
            $locationFrom = LocationProduct::query()
                ->where('company_id', $currentCompany->company_id)
                ->where('location_id', $request->input('location_from.store'))
                ->when($request->input('location_from.section'), function ($query) use ($request) {
                    return $query->where('sub_location_id', $request->input('location_from.section'));
                })
                ->where('product_id', $request->input('product'))
                ->firstOrFail();

            $locationTo = LocationProduct::query()
                ->where('company_id', $currentCompany->company_id)
                ->where('location_id', $request->input('location_to.store'))
                ->when($request->input('location_to.section'), function ($query) use ($request) {
                    return $query->where('sub_location_id', $request->input('location_to.section'));
                })
                ->where('product_id', $request->input('product'))
                ->first();

            if ($locationTo) {
                $locationTo->update([
                    'in_stock' => $locationTo->in_stock + $request->input('quantity')
                ]);
            } else {
                $locationTo = LocationProduct::create([
                    'company_id' => $currentCompany->company_id,
                    'location_id' => $request->input('location_to.store'),
                    'product_id' => $request->input('product'),
                    'sub_location_id' => $request->input('location_to.section'),
                    'in_stock' => $request->input('quantity'),
                ]);
            }

            $inventoryStockMovement = InventoryStockMovement::create([
                'company_id' => $currentCompany->company_id,
                'product_id' => $request->input('product'),
                'from_location_id' => $locationFrom->location_id,
                'from_section_id' => $locationFrom->sub_location_id,
                'to_location_id' => $locationTo->location_id,
                'to_section_id' => $locationTo->sub_location_id,
                'user_id' => Auth::id(),
                'number' => InventoryStockMovement::query()
                    ->where('company_id', $currentCompany->company_id)
                    ->max('number') + 1,
                'old_quantity' => $locationFrom->in_stock,
                'new_quantity' => $locationFrom->in_stock - $request->input('quantity'),
                'quantity' => $request->input('quantity'),
                'date' => now()->format('Y-m-d'),
                'remarks' => $request->input('remarks'),
            ]);

            $locationFrom->update([
                'in_stock' => $locationFrom->in_stock - $request->input('quantity')
            ]);
        });

        return new JsonResponse(
            $inventoryStockMovement
                ->load(
                    [

                        'product' => fn ($q) => $q->select(['id']),
                        'locationFrom' => fn ($q) => $q->select(['id', 'name as storeFrom']),
                        'sectionFrom' => fn ($q) => $q->select(['id', 'section_name', 'quantity']),
                        'locationTo' => fn ($q) => $q->select(['id', 'name as storeTo']),
                        'sectionTo' => fn ($q) => $q->select(['id', 'section_name', 'quantity']),
                        'user' => fn ($q) => $q->select(['id', 'name as user_name']),

                    ]
                )
        );
    }

    /**
     * Show
     *
     * Display the specified inventory stock movement.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function show(ShowRequest $request, int $id): JsonResponse
    {
        $inventoryStockMovements = InventoryStockMovement::query()->find($id);

        return new JsonResponse(
            [
                'payload' =>
                $inventoryStockMovements->load(
                    [
                        'product' => fn ($q) => $q->select(['id', 'name', 'product_code as code']),
                        'locationFrom' => fn ($q) => $q->select(['id', 'name as store']),
                        'sectionFrom' => fn ($q) => $q->select(['id', 'section_name', 'quantity']),
                        'locationTo' => fn ($q) => $q->select(['id', 'name as store']),
                        'sectionTo' => fn ($q) => $q->select(['id', 'section_name', 'quantity']),
                        'user' => fn ($q) => $q->select(['id', 'name']),
                    ]
                )
            ]
        );
    }

    /**
     * Export
     *
     * Export all inventory stock movements from storage to csv(xslt).
     * @authenticated
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(ExportRequest $request): BinaryFileResponse
    {
        $requestData = $request->input('filters');

        $filteredTransfers = $this->stockTransferService->exportTransfers($requestData);

        return Excel::download(new TransfersExport($filteredTransfers), 'StockTransfersExport.xlsx');
    }
}
