<?php

namespace App\Services;

use App\Models\LocationProduct;
use App\Models\Product;
use App\Traits\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{

    /**
     * @param Request $request
     * @return Collection|array
     */
    public function getInventoriesToExport(Request $request): Collection|array
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $query = Product::query();
        $query->with(
            [
                'category' => fn($q) => $q->select(['id', 'name', 'number']),
                'locations' => fn($q) => $q->select(['locations.id', 'name as store']),
                'locations.sections' => fn($q) => $q->select(
                    ['id', 'location_id', 'section_name as name', DB::raw('SUM(quantity) as quantity')]
                )->groupBy('id', 'location_id', 'name')
            ]
        )
            ->join('location_product', 'products.id', '=', 'location_product.product_id')
            ->join('locations', 'locations.id', '=', 'location_product.location_id')
            ->leftJoin('sub_locations', 'sub_locations.location_id', '=', 'locations.id')
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
            ->where('products.company_id', $currentCompany->company_id)
            ->groupBy(
                'products.id',
                'products.category_id',
                'products.name',
                'products.product_code',
                'products.cost_price'
            );

        if ($request->input('is_all') === true) {
            return match ($request->input('format')) {
                'excel' => $query->get(),
                default => $query->get()->toArray(),
            };
        }

        if ($request->input('custom_number')) {
            return match ($request->input('format')) {
                'excel' => $query->limit($request->input('custom_number'))->get(),
                default => $query->limit($request->input('custom_number'))->get()->toArray(),
            };
        }

        if ($request->has('selected') && is_array($request->input('selected')) && count($request->input('selected')) > 0) {
            $query->whereIn('products.id', $request->input('selected'));
        } else {
            return [];
        }

        return match ($request->input('format')) {
            'excel' => $query->get(),
            default => $query->get()->toArray(),
        };
    }

    /**
     * @param Request $request
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function inventoryParseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
            $categories = $parsedData->categories ?? [];
            $sellingPriceRange = $parsedData->selling_price_range ?? [];
            $quantityRange = $parsedData->stock_range ?? [];
        }

        return [
            'search' => $search ?? '',
            'categories' => $categories ?? [],
            'selling_price_range' => $sellingPriceRange ?? [],
            'stock_range' => $quantityRange ?? [],
        ];
    }

    public function getCurrentCompany(): mixed
    {
        return Auth::user()->companies()->newPivotStatement()
            ->where('is_default', true)
            ->where('user_id', Auth::id())->first();
    }
}
