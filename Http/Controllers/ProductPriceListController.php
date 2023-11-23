<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPriceListStoreRequest;
use App\Http\Requests\ProductPriceListUpdateRequest;
use App\Http\Resources\ProductPriceListResource;
use App\Models\Product;
use App\Models\ProductPriceList;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Product price list
 *
 * Endpoints for managing product price lists
 */
class ProductPriceListController extends Controller
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
     * Returns list of available product price lists
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse($request, ProductPriceListResource::collection(ProductPriceList::all()));
    }

    /**
     * Create
     *
     * Store a newly created product price list in storage.
     * @authenticated
     * @param  ProductPriceListStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProductPriceListStoreRequest $request): JsonResponse
    {
        $priceList = ProductPriceList::query()->create($request->validated());

        if ($request->has('product_id'))
        {
            $product = Product::query()->find($request->get('product_id'));
            $product->priceList()->save($priceList);
        }

        return new JsonResponse(['payload' => $priceList]);
    }

    /**
     * Show
     *
     * Display the specified product price list.
     * @authenticated
     * @param ProductPriceList $productPriceList
     * @return JsonResponse
     */
    public function show(ProductPriceList $productPriceList): JsonResponse
    {
        return new JsonResponse(['payload' => ProductPriceListResource::make($productPriceList)]);
    }

    /**
     * Edit
     *
     * Update the specified product price list in storage.
     * @authenticated
     * @param ProductPriceListUpdateRequest $request
     * @param ProductPriceList $productPriceList
     * @return JsonResponse
     */
    public function update(ProductPriceListUpdateRequest $request,ProductPriceList $productPriceList): JsonResponse
    {
        $productPriceList->update($request->all());

        return new JsonResponse(['payload' => $productPriceList]);
    }

    /**
     * Delete
     *
     * Remove the specified product price list from storage.
     * @authenticated
     * @param ProductPriceList $productPriceList
     * @return JsonResponse
     */
    public function destroy(ProductPriceList $productPriceList): JsonResponse
    {
        $productPriceList->delete();

        return new JsonResponse(['payload' => $productPriceList]);
    }
}
