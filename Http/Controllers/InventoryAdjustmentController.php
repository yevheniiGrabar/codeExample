<?php

namespace App\Http\Controllers;

use App\Enums\AdjustmentTypeEnum;
use App\Http\Requests\Adjustment\IndexRequest;
use App\Http\Requests\Adjustment\StoreRequest;
use App\Http\Requests\Product\ShowRequest;
use App\Http\Resources\InventoryAdjustmentDetailsResource;
use App\Http\Resources\InventoryAdjustmentResource;
use App\Models\InventoryAdjustment;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Services\InventoryAdjustmentService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * @group Inventory adjustment
 *
 * Endpoints for managing inventory adjustments
 */
class InventoryAdjustmentController extends Controller
{
    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;
    /** @var InventoryAdjustmentService */
    public InventoryAdjustmentService $inventoryAdjustmentService;
    private ConnectionInterface $connection;

    public function __construct(
        JsonResponseDataTransform $dataTransform,
        InventoryAdjustmentService $inventoryAdjustmentService,
        ConnectionInterface $connection
    ) {
        $this->connection = $connection;
        $this->dataTransform = $dataTransform;
        $this->inventoryAdjustmentService = $inventoryAdjustmentService;
    }

    /**
     * List
     *
     * Returns list of available inventory adjustments
     * @authenticated
     * @param  IndexRequest  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        if ($request->has('filters')) {
            $parsedRequest = json_decode($request->get('filters'));
        }
        $inventoryAdjustments = InventoryAdjustment::filter(
            $parsedRequest->search ?? null,
            $parsedRequest->product ?? null,
            $parsedRequest->location ?? null,
            $parsedRequest->remarks ?? null,
            $parsedRequest->date ?? null,
        )->where('company_id', $currentCompany->company_id)->orderBy('id', 'desc')->get();


        return $this->dataTransform->conditionalResponse(
            $request,
            InventoryAdjustmentResource::collection(
                $inventoryAdjustments
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created inventory adjustment in storage.
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $adjustment = null;

        $this->connection->transaction(function () use ($request, &$adjustment) {
            $currentCompany = CurrentCompany::getDefaultCompany();
            $product = Product::query()
                ->where('company_id', $currentCompany->company_id)
                ->where('id', $request->input('product_id'))
                ->firstOrFail();
            $locationProduct = LocationProduct::query()
                ->where('company_id', $currentCompany->company_id)
                ->where('location_id', $request->input('location.store_id'))
                ->when($request->input('location.section_id'), function ($query) use ($request) {
                    return $query->where('sub_location_id', $request->input('location.section_id'));
                })
                ->where('product_id', $request->input('product_id'))
                ->firstOrFail();

            $createData = [
                'company_id' => $currentCompany->company_id,
                'product_id' => $request->input('product_id'),
                'location_id' => $request->input('location.store_id'),
                'sub_location_id' => $request->input('location.section_id'),
                'user_id' => Auth::id(),
                'date' => now()->format('Y-m-d'),
                'adjustment_type' => $request->input('adjustment_type') ? 1 : 0,
                'remarks' => $request->input('remarks'),
                'old_quantity' => $locationProduct->in_stock,
                'actual_quantity' => $locationProduct->in_stock,
                'old_cost_price' => $product->cost_price,
                'actual_cost_price' => $product->cost_price,
            ];

            if ($request->input('adjustment_type') == AdjustmentTypeEnum::QUANTITY) {
                $createData['actual_quantity'] = $request->input('changed_value');

                $locationProduct->update([
                    'in_stock' => $request->input('changed_value'),
                ]);
            } else if ($request->input('adjustment_type') == AdjustmentTypeEnum::COST_PRICE) {
                $createData['actual_cost_price'] =  $request->input('changed_value');

                $product->update([
                    'cost_price' => $request->input('changed_value')
                ]);
            }

            $adjustment = InventoryAdjustment::create($createData);
        });

        return new JsonResponse(['payload' => new InventoryAdjustmentDetailsResource($adjustment)]);
    }

    /**
     * Show
     *
     * Display the specified inventory adjustment.
     * @authenticated
     * @param  InventoryAdjustment  $adjustment
     * @return JsonResponse
     */
    public function show(ShowRequest $request, InventoryAdjustment $adjustment): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $adjustment->where('company_id', $currentCompany->company_id);

        if (!$adjustment) {
            return new JsonResponse(['error' => 'Model not found'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['payload' => InventoryAdjustmentDetailsResource::make($adjustment)]);
    }

    /**
     * Product location
     *
     * Display location of the specified product.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function getProductLocation(int $id): JsonResponse
    {
        $location = Location::query()
            ->where('product_id', $id)
            ->get();

        return new JsonResponse(['payload' => $location]);
    }

    /**
     * Product quantity
     *
     * Display quantity and stock price of the specified product.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function getProductQuantity(int $id): JsonResponse
    {
        $quantity = Product::query()->join('sub_locations', 'products.id',
            '=', 'sub_locations.product_id')->where('sub_locations.product_id', $id)->sum('quantity');
        $costPrice = Product::query()->where('id', $id)->sum('cost_price');

        return new JsonResponse(
            [
                'payload' => ['product' => ['quantity' => $quantity, 'cost_price' => $costPrice]]
            ]
        );
    }
}
