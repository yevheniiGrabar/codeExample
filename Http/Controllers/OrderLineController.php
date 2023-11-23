<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderLineResource;
use App\Models\OrderLine;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @group Order line
 *
 * Endpoints for managing order lines
 */
class OrderLineController extends Controller
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
     * Returns list of available order lines
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            OrderLineResource::collection(
                OrderLine::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created order line in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
//        $this->authorize('create', OrderLine::class);

        // @todo add validation
        $orderLine = OrderLine::query()->create($request->all());

        if ($request->has('purchase_order_id')) {
            $orderLine->purchase_order_id = $request->get('purchase_order_id');
            $orderLine->save();
        }

        return new JsonResponse(new OrderLineResource($orderLine), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified order line.
     * @authenticated
     * @param OrderLine $orderLine
     * @return JsonResponse
     */
    public function show(OrderLine $orderLine): JsonResponse
    {
        return new JsonResponse(
            $orderLine->load(
                [
                    'product' => fn($q) => $q->select(['id', 'name', 'tax_id']),
//                    'product.defaultTax' => fn($q) => $q->select(['id'])
                ]
            ), Response::HTTP_OK
        );
    }

    /**
     * Edit
     *
     * Update the specified order line in storage.
     * @authenticated
     * @param Request $request
     * @param OrderLine $orderLine
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request, OrderLine $orderLine): JsonResponse
    {
        $orderLine->updateOrFail($request->all());

        return new JsonResponse(new OrderLineResource($orderLine), Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified order line from storage.
     * @authenticated
     * @param OrderLine $orderLine
     * @return JsonResponse
     */
    public function destroy(OrderLine $orderLine): JsonResponse
    {
        $orderLine->delete();

        return new JsonResponse(['message' => 'OrderLine deleted successfully'], Response::HTTP_OK);
    }
}
