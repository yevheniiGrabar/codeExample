<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierInvoiceStoreRequest;
use App\Http\Requests\Supplier\SupplierInvoiceUpdateRequest;
use App\Http\Resources\SupplierInvoiceResource;
use App\Models\SupplierInvoice;
use App\Services\InvoiceService;
use App\Services\JsonResponseDataTransform;
use App\Services\SupplierInvoiceRequestParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @group Supplier invoice
 *
 * Endpoints for managing supplier invoice
 */
class SupplierInvoiceController extends Controller
{

    /** @var InvoiceService */
    public InvoiceService $invoiceService;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var SupplierInvoiceRequestParser */
    public SupplierInvoiceRequestParser $requestParser;

    public function __construct(
        InvoiceService $invoiceService,
        JsonResponseDataTransform $dataTransform,
        SupplierInvoiceRequestParser $requestParser
    ) {
        $this->invoiceService = $invoiceService;
        $this->dataTransform = $dataTransform;
        $this->requestParser = $requestParser;
    }

    /**
     * List
     *
     * Returns list of available supplier invoices
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $parsedRequest = $this->requestParser->parseRequest($request);
        $invoices = SupplierInvoice::filter(
            $parsedRequest['supplier'],
            $parsedRequest['currency'],
            $parsedRequest['status'],
            $parsedRequest['date'],
        )->with(
            [
                'supplier' => fn($q) => $q->select(['id', 'company_name']),
                'purchaseOrder' => fn($q) => $q->select(['id', 'total', 'supplier_invoice_id']),
                'currency' => fn($q) => $q->select(['id', 'name']),
            ]
        )->orderBy('id', 'desc')->get();

        return $this->dataTransform->conditionalResponse($request, SupplierInvoiceResource::collection($invoices));
    }

    /**
     * Create
     *
     * Store a newly created supplier invoice in storage.
     * @authenticated
     * @param SupplierInvoiceStoreRequest $request
     * @return JsonResponse
     */
    public function store(SupplierInvoiceStoreRequest $request): JsonResponse
    {
        //@todo update SupplierInvoiceStoreRequest & table according new design
        $invoice = SupplierInvoice::query()->create($request->validated());


        return new JsonResponse(new SupplierInvoiceResource($invoice), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified supplier invoice.
     * @authenticated
     * @param SupplierInvoice $invoice
     * @return JsonResponse
     */
    public function show(SupplierInvoice $invoice): JsonResponse
    {
        return new JsonResponse($invoice->load('invoiceLine'));
    }

    /**
     * Edit
     *
     * Update the specified supplier invoice in storage.
     * @authenticated
     * @param SupplierInvoiceUpdateRequest $request
     * @param SupplierInvoice $invoice
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SupplierInvoiceUpdateRequest $request, SupplierInvoice $invoice): JsonResponse
    {
        //@todo update SupplierInvoiceUpdateRequest
        $invoice->updateOrFail($request->validated());

        return new JsonResponse(new SupplierInvoiceResource($invoice), Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified supplier invoice from storage.
     * @authenticated
     * @param SupplierInvoice $invoice
     * @return JsonResponse
     */
    public function destroy(SupplierInvoice $invoice): JsonResponse
    {
        $invoice->delete();

        return new JsonResponse(['message' => 'SupplierInvoice Deleted'], Response::HTTP_OK);
    }
}
