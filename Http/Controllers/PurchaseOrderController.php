<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrdersExport;
use App\Http\Requests\PurchaseOrder\IndexRequest;
use App\Http\Requests\PurchaseOrder\StoreRequest;
use App\Http\Requests\PurchaseOrder\UpdateRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\UpcomingShipmentsResource;
use App\Imports\PurchaseOrdersImport;
use App\Models\PurchaseOrder;
use App\Services\JsonResponseDataTransform;
use App\Services\PurchaseOrderRequestParser;
use App\Services\PurchaseOrderService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Purchase order
 *
 * Endpoints for managing purchase orders
 */
class PurchaseOrderController extends Controller
{
    /** @var PurchaseOrderService */
    public PurchaseOrderService $purchaseOrderService;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $jsonResponseDataTransform;

    /** @var PurchaseOrderRequestParser */
    public PurchaseOrderRequestParser $requestParser;

    public function __construct(
        PurchaseOrderService       $purchaseOrderService,
        JsonResponseDataTransform  $jsonResponseDataTransform,
        PurchaseOrderRequestParser $requestParser,
    )
    {
        $this->purchaseOrderService = $purchaseOrderService;
        $this->jsonResponseDataTransform = $jsonResponseDataTransform;
        $this->requestParser = $requestParser;
    }

    /**
     * List
     *
     * Returns list of available purchase orders
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $parsedRequest = $this->requestParser->parseRequest($request);
        $currentCompany = CurrentCompany::getDefaultCompany();


        $purchaseOrders = PurchaseOrder::filter(
            $parsedRequest['suppliers'],
            $parsedRequest['invoiced'],
            $parsedRequest['received'],
            $parsedRequest['from'],
            $parsedRequest['to'],
            $parsedRequest['search']
        )
            ->with(
                [
                    'supplier' => fn($q) => $q->select('id', 'name', 'code'),
                    'currency' => fn($q) => $q->select(['id', 'name', 'code']),
                    'lines' => fn($q) => $q->select(
                        ['id', 'purchase_order_id', 'tax_id', 'product_id', 'quantity', 'unit_price', 'discount']
                    ),
                    'lines.product' => fn($q) => $q->select(['id', 'name', 'product_code as code', 'has_serial_number', 'has_batch_number']),
                    'lines.tax' => fn($q) => $q->select(['id', 'rate']),
                ]
            );

        if (!empty($parsedRequest['orderByField']) && !empty($parsedRequest['orderByType'])) {
            if ($parsedRequest['orderByType'] !== 'asc' && $parsedRequest['orderByType'] !== 'desc') {
                throw new \InvalidArgumentException('Order direction must be "asc" or "desc".');
            }
            $purchaseOrders = $purchaseOrders->orderBy('suppliers' . $parsedRequest['orderByField'], $parsedRequest['orderByType']);
        } else {
            $purchaseOrders->orderByDesc('id');
        }

        $purchaseOrders = $purchaseOrders->where('company_id', $currentCompany->company_id)->get();

        return $this->jsonResponseDataTransform->conditionalResponse(
            $request,
            PurchaseOrderResource::setMode('single')::collection($purchaseOrders)
        );
    }

    /**
     * Create
     *
     * Store a newly created purchase order in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($request->validated());
        $loadPurchaseOrderData = $this->purchaseOrderService->loadAdditionalData($purchaseOrder);

        return new JsonResponse(['payload' => PurchaseOrderResource::setMode('details')::make($loadPurchaseOrderData)]);
    }

    /**
     * Show
     *
     * Display the specified purchase order.
     * @authenticated
     * @param PurchaseOrder $purchaseOrder
     * @return JsonResponse
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $loadPurchaseOrderData = $this->purchaseOrderService->loadAdditionalData($purchaseOrder);

        return new JsonResponse(['payload' => PurchaseOrderResource::setMode('details')::make($loadPurchaseOrderData)]);
    }

    /**
     * Edit
     *
     * Update the specified purchase order in storage.
     * @authenticated
     * @param Request $request
     * @param PurchaseOrder $purchaseOrder
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $updatePurchaseOrder = $this->purchaseOrderService->updatePurchaseOrder($request->validated(), $purchaseOrder);
        $loadData = $this->purchaseOrderService->loadAdditionalData($updatePurchaseOrder);

        return new JsonResponse(['payload' => PurchaseOrderResource::setMode('details')::make($loadData)]);
    }

    /**
     * Delete
     *
     * Remove the specified purchase order from storage.
     * @authenticated
     * @param PurchaseOrder $purchaseOrder
     * @return JsonResponse
     */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->lines()->delete();
        $purchaseOrder->delete();

        return new JsonResponse([]);
    }

    /**
     * Import
     *
     * Import all purchase orders from csv(xslt) to storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        Excel::import(new PurchaseOrdersImport(), $request->file('import')->store('files'));

        return new JsonResponse(['message' => 'Products imported successfully']);
    }

    /**
     * Export
     *
     * Export all purchase orders from storage to csv(xslt).
     * @authenticated
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new PurchaseOrdersExport(), 'ProductionOrderExport.xlsx');
    }


    /**
     * Upcoming shipments
     *
     * Returns list of upcoming shipments
     * @authenticated
     * @return JsonResponse
     */
    public function upcomingShipments(): JsonResponse
    {
        $shipments = PurchaseOrder::upcomingShipments();

        return new JsonResponse(['payload' => UpcomingShipmentsResource::collection($shipments)]);
    }
}
