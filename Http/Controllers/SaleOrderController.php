<?php

namespace App\Http\Controllers;

use App\Exports\SaleOrderExport;
use App\Http\Requests\Product\DestroyRequest;
use App\Http\Requests\SaleOrder\ExportRequest;
use App\Http\Requests\SaleOrder\IndexRequest;
use App\Http\Requests\SaleOrder\ShowRequest;
use App\Http\Requests\SaleOrder\StoreRequest;
use App\Http\Requests\SaleOrder\UpdateRequest;
use App\Http\Resources\CustomerSaleOrdersResource;
use App\Http\Resources\SaleOrderResource;
use App\Models\Company;
use App\Models\SaleOrder;
use App\Models\SaleOrderLine;
use App\Services\JsonResponseDataTransform;
use App\Services\SaleOrderRequestParser;
use App\Services\SaleOrderService;
use App\Support\PowerOffice\PowerOffice;
use App\Support\Shipmondo\Shipmondo;
use App\Traits\CurrentCompany;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Sale order
 *
 * Endpoints for managing sale orders
 */
class SaleOrderController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    /** @var SaleOrderRequestParser */
    public SaleOrderRequestParser $requestParser;

    /**
     * @var SaleOrderService
     */
    public SaleOrderService $saleOrderService;

    public function __construct(
        JsonResponseDataTransform $dataTransform,
        SaleOrderRequestParser $requestParser,
        SaleOrderService $saleOrderService,
    ) {
        $this->dataTransform = $dataTransform;
        $this->requestParser = $requestParser;
        $this->saleOrderService = $saleOrderService;
    }

    /**
     * List
     *
     * Returns list of available sale orders
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        if ($request->has('filters')) {
            $parsedRequest = json_decode($request->get('filters'));
        }

        $saleOrders = SaleOrder::filter(
            $parsedRequest->customers ?? null,
            $parsedRequest->search ?? null,
            $parsedRequest->dates ?? null,
        )
            ->with(
                [
                    'customer' => fn($q) => $q->select('*'),
                    'currency' => fn($q) => $q->select(['id', 'name', 'code']),
                    'lines' => fn($q) => $q->select(
                        [
                            'id',
                            'sale_order_id',
                            'tax_id',
                            'product_id',
                            'quantity',
                            'unit_price',
                            'discount'
                        ] //'tax_id',
                    ),
                    'lines.product' => fn($q) => $q->select(['id', 'name', 'product_code as code']),
                    'lines.tax' => fn($q) => $q->select(['id', 'rate']),

                ]
            )
            ->where('company_id', $defaultCompany->company_id)->orderBy('id', 'desc')
            ->get();

        return $this->dataTransform->conditionalResponse(
            $request,
            SaleOrderResource::setMode('single')::collection($saleOrders)
        );
    }

    /**
     * Create
     *
     * Store a newly created sale order in storage.
     * @authenticated
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $saleOrder = $this->saleOrderService->createSaleOrder($request->validated());
        $loadSaleOrderData = $this->saleOrderService->loadAdditionalData($saleOrder);

        $powerOffice = new PowerOffice();
        $shipmondo = new Shipmondo();

        if($powerOffice->isIntegrated()) {
            $powerOffice->fetchOutgoingInvoice($saleOrder);
        }

        if($shipmondo->isIntegrated()) {
            $shipmondo->createSaleOrder($saleOrder);
//            $shipmondo->createShipment($saleOrder->shipmondo_id);
        }

        return new JsonResponse(['payload' => SaleOrderResource::setMode('details')::make($loadSaleOrderData)]);
    }

    /**
     * Show
     *
     * Display the specified sale order.
     * @authenticated
     * @param  SaleOrder  $sale
     * @return JsonResponse
     */
    public function show(ShowRequest $request, SaleOrder $sale): JsonResponse
    {
        $loadOrderData = $this->saleOrderService->loadAdditionalData($sale);

        if (!$loadOrderData) {
            return new JsonResponse(['error' => 'Something wrong'], 422);
        }

        return new JsonResponse(
            [
                'payload' => SaleOrderResource::setMode('details')::make($loadOrderData)
            ]
        );
    }

    /**
     * Edit
     *
     * Update the specified sale order in storage.
     * @authenticated
     * @param  UpdateRequest  $request
     * @param  SaleOrder  $sale
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, SaleOrder $sale): JsonResponse
    {
        $saleOrder = $this->saleOrderService->updateSaleOrder($request->validated(), $sale);
        if (!$saleOrder) {
            return new JsonResponse(['error' => 'Something wrong'], 422);
        }

        $loadData = $this->saleOrderService->loadAdditionalData($saleOrder);

        if (!$loadData) {
            return new JsonResponse(['error' => 'Try again later'], 422);
        }

        return new JsonResponse(['payload' => SaleOrderResource::setMode('details')::make($loadData)]);
    }

    /**
     * Delete
     *
     * Remove the specified sale order from storage.
     * @authenticated
     * @param  SaleOrder  $sale
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, SaleOrder $sale): JsonResponse
    {
        $lines = SaleOrderLine::query()->where('sale_order_id', $sale->id)->delete();
        $sale->delete();

        return new JsonResponse(['message' => 'Sale Order deleted successfully']);
    }

    /**
     * Export
     *
     * Export all sale orders from storage to csv(xslt).
     * @authenticated
     * @return BinaryFileResponse
     */
    public function export(ExportRequest $request): BinaryFileResponse
    {
        return Excel::download(new SaleOrderExport(), 'SaleOrder.xlsx');
    }

    /**
     * Products
     *
     * Display products of the specified sale order.
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function products(Request $request): JsonResponse
    {
        $requestedData = $request->validate(
            [
                'customer_id' => 'required|integer',
            ]
        );

        $customerId = $requestedData['customer_id'];
        return new JsonResponse(
            ['payload' => $this->saleOrderService->getProducts($customerId)], ResponseAlias::HTTP_OK
        );
    }

    /**
     * Customer
     *
     * Display customer of the specified sale order.
     * @authenticated
     * @param $id
     * @return JsonResponse
     */
    public function customerSaleOrders($id): JsonResponse
    {
        $saleOrders = SaleOrderResource::collection($this->saleOrderService->getSaleOrders($id));
        return new JsonResponse(
            ['payload' => CustomerSaleOrdersResource::collection($saleOrders)],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * Preview PDF
     *
     * Generate Pdf file by id param to preview & download(pdf)
     * @authenticated
     * @return Response|BinaryFileResponse
     */
    public function previewPdf($id): Response|BinaryFileResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $company = Company::query()->findOrFail($currentCompany->company_id);
        $saleOrder = SaleOrder::query()->find($id);
        $saleOrder->loadMissing(['customer', 'deliveryAddress']);

        $data = [
            'saleOrder' => $saleOrder,
            'customer' => $saleOrder->customer,
            'deliveryAddress' => $saleOrder->deliveryAddress,
            'company' => $company,
            'items' => $saleOrder->lines,
        ];

        $pdf = Pdf::loadView('PDF.saleOrderPreview', $data);

        return $pdf->download('SaleOrder.pdf');
    }
}
