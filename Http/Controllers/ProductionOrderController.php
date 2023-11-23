<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductionOrderResource;
use App\Models\ProductionOrder;
use App\Services\JsonResponseDataTransform;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Production order
 *
 * Endpoints for managing production orders
 */
class ProductionOrderController extends Controller
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
     * Returns list of available production orders
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
//        dd(ProductionOrder::with('stock')->get());
//        $this->authorize('viewAny', ProductionOrder::class);
        return $this->dataTransform->conditionalResponse(
            $request,

            ProductionOrder::query()->with(
                ['product' => fn($q) => $q->select(['id', 'name', 'product_qty', 'cost_price'])]
            )->orderBy('id','desc')->get()
        );
    }

    /**
     * Create
     *
     * Store a newly created production order in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
//        $this->authorize('create', ProductionOrder::class);

        $component = '';
        $productionOrder = ProductionOrder::query()->create(
            [
                'number' => $request->get('number'),
                'barcode' => $request->get('barcode'),
                'in_order' => $request->get('in_order'),
                'available' => $request->get('available'),
                'in_production' => $request->get('in_production'),
                'user_id' => $request->get('user_id'),
            ]
        );
        if ($request->has('product_id')) {
            $productionOrder->product_id = $request->get('product_id');
            $productionOrder->save();
        }


//        if ($request->has('component_ids')) {
//            $componentIds = explode(',', $request->get('component_ids'));
//
//            if (!empty($componentIds) && sizeof($componentIds) > 0) {
//                foreach ($componentIds as $componentId) {
//                    $component = Product::query()->find($componentId);
//                    $component->production_order_id = $component->id;
//                    $component->save();
//                }
//            }
//        }

        return new JsonResponse(new ProductionOrderResource($productionOrder));
    }

    /**
     * Show
     *
     * Display the specified production order.
     * @authenticated
     * @param ProductionOrder $productionOrder
     * @return JsonResponse
     */
    public function show(ProductionOrder $productionOrder): JsonResponse
    {
//        $this->authorize('view', ProductionOrder::class);

        return new JsonResponse(ProductionOrderResource::make($productionOrder));
    }

    /**
     * Edit
     *
     * Update the specified production order in storage.
     * @authenticated
     * @param Request $request
     * @param ProductionOrder $productionOrder
     * @return JsonResponse
     */
    public function update(Request $request, ProductionOrder $productionOrder): JsonResponse
    {
//        $this->authorize('update', ProductionOrder::class);

        $productionOrder->update($request->all());

        return new JsonResponse(new ProductionOrderResource($productionOrder));
    }

    /**
     * Delete
     *
     * Remove the specified production order from storage.
     * @authenticated
     * @param ProductionOrder $productionOrder
     * @return JsonResponse
     */
    public function destroy(ProductionOrder $productionOrder): JsonResponse
    {
//        $this->authorize('delete', ProductionOrder::class);
        $productionOrder->delete();

        return new JsonResponse([]);
    }
}
