<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceLineResource;
use App\Models\InvoiceLine;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Invoice line
 *
 * Endpoints for managing invoice lines
 */
class InvoiceLineController extends Controller
{

    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available invoice lines
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $invoiceLines = InvoiceLine::with(
            [
                'products' => fn($q) => $q->select(
                    ['id', 'product_code', 'name', 'product_qty', 'cost_price', 'tax_id']
                ),
                'products.tax' => fn($q) => $q->select(['id', 'rate']),
            ]
        )->orderBy('id', 'desc')->get();
        return $this->dataTransform->conditionalResponse($request, $invoiceLines);
    }

    /**
     * Create
     *
     * Store a newly created invoice line in storage.
     * @authenticated
     * @param  Request  $request
     * @return InvoiceLineResource
     */
    public function store(Request $request): InvoiceLineResource
    {
        $invoiceLine = InvoiceLine::query()->create($request->all());

        return new InvoiceLineResource($invoiceLine);
    }

    /**
     * Show
     *
     * Display the specified invoice line.
     * @authenticated
     */
    public function show(InvoiceLine $invoiceLine): InvoiceLineResource
    {
        return InvoiceLineResource::make($invoiceLine);
    }

    /**
     * Edit
     *
     * Update the specified invoice line in storage.
     * @authenticated
     * @param  Request  $request
     * @param  InvoiceLine  $invoiceLine
     * @return InvoiceLineResource
     */
    public function update(Request $request, $invoiceLine): InvoiceLineResource
    {
        $invoiceLine->update($request->all());

        return new InvoiceLineResource($invoiceLine);
    }

    /**
     * Delete
     *
     * Remove the specified invoice line from storage.
     * @authenticated
     * @param InvoiceLine $invoiceLine
     * @return JsonResponse
     */
    public function destroy($invoiceLine): JsonResponse
    {
        $invoiceLine->delete();

        return new JsonResponse([]);
    }
}
