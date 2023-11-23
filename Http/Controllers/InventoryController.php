<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Http\Resources\InventoryResource;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Inventory
 *
 * Endpoints for managing inventory
 */
class InventoryController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public InventoryService $inventoryService;

    public function __construct(JsonResponseDataTransform $dataTransform, InventoryService $inventoryService)
    {
        $this->dataTransform = $dataTransform;
        $this->inventoryService = $inventoryService;
    }

    /**
     * List
     *
     * Returns list of available products in inventory
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $parsedRequest = $this->inventoryService->inventoryParseRequest($request);
        $currentCompany = CurrentCompany::getDefaultCompany();
        $orderByEncoded = isset(($request->input('orderBy'))[0]) ? ($request->input('orderBy'))[0] : null;
        $orderBy = json_decode($orderByEncoded, true);
        $products = Product::query()
            ->filter($parsedRequest)
            ->with([
                'category',
                'locations.sections'
            ])
            ->where('company_id', $currentCompany->company_id)
            ->productOrderBy($orderBy)
            ->get()
            ->when(!empty($parsedRequest['stock_range']), function ($items) use ($parsedRequest) {
                return $items->whereBetween('in_stock', $parsedRequest['stock_range']);
            })
            ->when(isset($orderBy['name']) && $orderBy['name'] === 'inStock', function ($items) use ($orderBy) {
                if($orderBy['type'] === 'asc')
                    return $items->sortBy(function ($item) {
                        return $item->locations()->sum('in_stock');
                    });
                else
                    return $items->sortByDesc(function ($item) {
                        return $item->locations()->sum('in_stock');
                    });
            });

        return $this->dataTransform->conditionalResponse($request, InventoryResource::collection($products));
    }

    /**
     * Show
     *
     * Display the inventory.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::query()->with(
            [
                'category' => fn($q) => $q->select(['id', 'name', 'number']),
                'locations' => fn($q) => $q->select(['locations.id', 'name']),
                'locations.sections' => fn($q) => $q->select(['id', 'location_id', 'section_name as name', 'quantity'])
            ]
        )
            ->join('location_product', 'products.id', '=', 'location_product.product_id')
            ->join('locations', 'locations.id', '=', 'location_product.location_id')
            ->join('sub_locations', 'sub_locations.location_id', '=', 'locations.id')
            ->select(
                [
                    'products.id',
                    'products.category_id',
                    'products.name',
                    'products.product_code as code',
                    'products.cost_price',
                    DB::raw('(SELECT SUM(in_stock) FROM location_product WHERE product_id = products.id) as in_stock')
                ]
            )
            ->where('products.id', $id)
            ->groupBy('products.id')
            ->findOrFail($id);

        return new JsonResponse(['payload' => $product]);
    }

    /**
     * Export
     *
     * Export all customers from storage to csv(xslt).
     * @authenticated
     * @param  Request  $request
     * @return BinaryFileResponse|JsonResponse
     */
    public function export(Request $request): BinaryFileResponse|JsonResponse
    {
        if ($request->get('is_all') || !empty($request->get('custom_number')) || !is_null($request->get('selected'))) {
            $inventories = $this->inventoryService->getInventoriesToExport($request);

            return Excel::download(new InventoryExport($inventories), 'InventoryExport.xlsx');
        }

        return new JsonResponse(['error' => 'At least one parameter must be provided.'], 500);
    }

    /**
     * Filter data
     *
     * Display data for inventory filters.
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getDataForFilters(Request $request): JsonResponse
    {
        $currentCompanyId = CurrentCompany::getDefaultCompany()->company_id;

        $minCostPrice = Product::where('company_id', $currentCompanyId)->min('cost_price');
        $maxCostPrice = Product::where('company_id', $currentCompanyId)->max('cost_price');

        $productsInStock = LocationProduct::query()
            ->where('company_id', $currentCompanyId)
            ->get()
            ->groupBy('product_id')
            ->map(function ($item) {
                return $item->sum('in_stock');
            })
            ->toArray();

        return new JsonResponse(
            [
                'payload' => [
                    'cost_price' => ['min' => $minCostPrice, 'max' => $maxCostPrice],
                    'quantity' => ['min' => min($productsInStock), 'max' => max($productsInStock)]
                ]
            ]
        );
    }

    public function filterByQuantity($products, $request)
    {
        $filters = json_decode($request->get('filters'));

        if (is_object($filters)) {
            $filters = (array) $filters;
        }

        if (!empty($filters['price_range'])) {
            $minPrice = $filters['price_range'][0];
            $maxPrice = $filters['price_range'][1];
            if ($minPrice !== null && $maxPrice !== null) {
                $products->where(
                    function ($q) use ($minPrice, $maxPrice) {
                        $q->where('cost_price', '>=', $minPrice, 'and')
                            ->where('cost_price', '<=', $maxPrice, 'and');
                    }
                );
            }

            return $products;
//            $query->whereBetween('cost_price', [$minPrice, $maxPrice]);
        }
        if (!empty($filters['categories'])) {
            $categories = $filters['categories'];
            $products->where('category_id', $categories, 'and');
        }

        if (!empty($filters['stock_range'])) {
            $minStock = $filters['stock_range'][0];
            $maxStock = $filters['stock_range'][1];

            if ($minStock !== null && $maxStock !== null) {
                $products->whereHas(
                    'locations',
                    function ($q) use ($minStock, $maxStock) {
                        $q->whereHas(
                            'subLocations',
                            function ($subQ) use ($minStock, $maxStock) {
                                $subQ->selectRaw('SUM(quantity) as total_quantity')
                                    ->whereBetween('quantity', [$minStock, $maxStock]);
                            }
                        );
                    }
                );
            }

            return $products;
        }
        return $products;
    }
}
