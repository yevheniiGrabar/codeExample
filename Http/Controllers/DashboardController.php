<?php

namespace App\Http\Controllers;

use App\Models\LocationProduct;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;

/**
 * @group Dashboard
 *
 * Endpoints for managing dashboard
 */
class DashboardController extends Controller
{
    public function revenue()
    {
    }

    public function getBestSellingProduct()
    {
        //
    }

    public function feed()
    {
        //
    }

    /**
     * @return JsonResponse
     */
    public function restocking(): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $products = LocationProduct::query()
            ->with(
                [
                    'products' => fn($q) => $q->select(['id', 'name', 'product_code as code']),
                    'locations' => fn($q) => $q->select(['id', 'name as store']),
                    'locations.sections' => fn($q) => $q->select(
                        ['id', 'location_id', 'section_name', 'row', 'sector', 'shelf_height', 'quantity']
                    )
                ]
            )->where('company_id', $currentCompany->company_id)
            ->whereRaw('in_stock  <= min_inventory_quantity')
            ->get();

        $data = [];

        foreach ($products as $product) {
            $productData = $product->toArray();

            $item['id'] = $productData['id'];
            $item['in_stock'] = $productData['in_stock'];
            $item['min_inventory_quantity'] = $productData['min_inventory_quantity'];
            $item['min_purchase_quantity'] = $productData['min_purchase_quantity'];
            $item['min_sale_quantity'] = $productData['min_sale_quantity'];
            $item['product']['id'] = $productData['products']['id'];
            $item['product']['name'] = $productData['products']['name'];
            $item['product']['code'] = $productData['products']['code'];
            $item['location']['store']['id'] = $productData['locations']['id'];
            $item['location']['store']['name'] = $productData['locations']['store'] ?? null;

            $sections = [];
            foreach ($productData['locations']['sections'] as $section) {
                $sections[] = [
                    'id' => $section['id'],
                    'name' => $section['section_name']
                ];
            }
            $item['location']['section'] = $sections;

            $data[] = $item;
        }
        return new JsonResponse(['payload' => $data]);
    }

    public function getStockRecordById(int $id): JsonResponse
    {
        $products = LocationProduct::query()
            ->with(
                [
                    'products' => fn($q) => $q->select(['id', 'name', 'product_code as code']),
                    'locations' => fn($q) => $q->select(['id', 'name as store']),
                    'locations.sections' => fn($q) => $q->select(
                        ['id', 'section_name', 'row', 'sector', 'height', 'quantity']
                    )
                ]
            )->where('location_product.product_id', '=', $id)
            ->whereRaw('in_stock  <= min_inventory_quantity')->first();


        return new JsonResponse(['payload' => $products]);
    }
}
