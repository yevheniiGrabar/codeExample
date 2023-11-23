<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleOrderLineResource;
use App\Models\SaleOrderLine;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Sale order line
 *
 * Endpoints for managing sale order lines
 */
class SaleOrderLineController extends Controller
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
     * Returns list of available sale order lines
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            SaleOrderLineResource::collection(SaleOrderLine::query()->with(['product', 'saleOrder'])
                ->orderBy('id', 'desc')
                ->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created sale order line in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $saleOrderLine = new SaleOrderLine();
        $saleOrderLine->collection_name = $request->get('collection_name') ?? null;
        $saleOrderLine->discount = $request->get('discount');
        $saleOrderLine->unit_price = $request->get('unit_price');
        $saleOrderLine->supplier_registration_no = $request->get('supplier_registration_no');
        $saleOrderLine->quantity = $request->get('quantity');

        if ($request->has('product_id')) {
            $saleOrderLine->product_id = $request->get('product_id');
        }
        if ($request->has('order_id')) {
            $saleOrderLine->sale_order_id = $request->get('order_id');
        }
        if ($request->has('location_id')) {
            $saleOrderLine->location_id = $request->get('location_id');
        }
        $saleOrderLine->save();

        return new JsonResponse(new SaleOrderLineResource($saleOrderLine));
    }

    /**
     * Show
     *
     * Display the specified sale order line.
     * @authenticated
     * @param SaleOrderLine $saleOrderLine
     * @return JsonResponse
     */
    public function show(SaleOrderLine $saleOrderLine): JsonResponse
    {
        return new JsonResponse(SaleOrderLineResource::make($saleOrderLine));
    }


    /**
     * Edit
     *
     * Update the specified sale order line in storage.
     * @authenticated
     * @param Request $request
     * @param SaleOrderLine $saleOrderLine
     * @return Response
     */
    public function update(Request $request, SaleOrderLine $saleOrderLine): Response
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified sale order line from storage.
     * @authenticated
     * @param SaleOrderLine $saleOrderLine
     * @return Response
     */
    public function destroy(SaleOrderLine $saleOrderLine): Response
    {
        //
    }
}
