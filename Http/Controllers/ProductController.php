<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\ProductAddPriceRequest;
use App\Http\Requests\ProductUpdatePriceRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductPriceHistoryResource;
use App\Http\Resources\ProductPurchaseOrdersResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductTranslationResource;
use App\Imports\ProductsImport;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Services\JsonResponseDataTransform;
use App\Services\ProductService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * @group Product
 *
 * Endpoints for managing products
 */
class ProductController extends Controller
{
    use CurrentCompany;

    /** @var ProductService */
    public ProductService $productService;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(ProductService $productService, JsonResponseDataTransform $dataTransform)
    {
        $this->productService = $productService;
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available products
     * @authenticated
     * @param  IndexRequest  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $parsedRequest = $this->productService->productParseRequest($request);
        $currentCompany = CurrentCompany::getDefaultCompany();

        $products = Product::filter($parsedRequest)->with('category')
            ->where('is_deleted', 0)
            ->where('company_id', $currentCompany->company_id)
            ->orderBy('id', 'desc')
            ->get();

        if ($request->has('slim') && $request->get('slim') != false) {
            return new JsonResponse(
                [
                    'payload' => ProductResource::setMode('single')::collection(
                        Product::query()->with(['orderLine', 'tax', 'currency'])->where(
                            'company_id',
                            $currentCompany->company_id
                        )->get()
                    )
                ]
            );
        }

        return $this->dataTransform->conditionalResponse(
            $request,
            ProductResource::collection(
                $products
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created product in storage.
     * @authenticated
     * @param  StoreRequest  $request
     * @return JsonResponse
     * @noinspection PhpUnusedLocalVariableInspection
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            $associateCompanyWithProduct = $this->productService->associateProductWithDefaultCompany($product);

            $product->load(['serialNumbers', 'batchNumbers']);
            return new JsonResponse(ProductResource::setMode('details')::make($product->loadMissing('currency')));
        } catch (ValidationException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Show
     *
     * Display the specified product.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();
        $product = Product::query()
            ->where('company_id', $defaultCompany->company_id)
            ->where('is_deleted', '==', 0)
            ->find($id);

        return new JsonResponse(
            ['payload' => ProductResource::setMode('details')::make($product->loadMissing('currency'))],
            Response::HTTP_OK
        );
    }

    /**
     * Delete
     *
     * Remove the specified product from storage.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::query()->find($id);

        $product->update(['is_deleted' => '1']);

        return new JsonResponse([]);
    }

    /**
     * Edit
     *
     * Update the specified product in storage.
     * @authenticated
     * @param  ProductUpdateRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::query()->find($id);
        $productData = $this->productService->updateProduct($product, $request->all());


        return new JsonResponse(ProductResource::make($productData));
    }

    /**
     * Import
     *
     * Import all products from csv(xslt) to storage.
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $import = new ProductsImport($request->input('mapping'));
        Excel::import($import, $request->file('import'));

        return new JsonResponse(['message' => 'Products imported successfully']);
    }

    /**
     * Export
     *
     * Export all products from storage to csv(xslt).
     * @authenticated
     * @param  Request  $request
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
    {
        $selected = $request->input('columns');

        return Excel::download(new ProductExport($selected), 'ProductExport.xlsx');
    }

    /**
     * Total quantity
     *
     * Display total quantity of the specified product.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function totalQuantity(int $id): JsonResponse
    {
        $totalQuantity = $this->productService->getSubLocationsTotalQuantityByProductId($id);

        return new JsonResponse($totalQuantity);
    }

    /**
     * Locations
     *
     * Display locations of the specified product.
     * @authenticated
     * @param  int  $id
     * @return JsonResponse
     */
    public function getProductLocations(int $id): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();
        $output = [];
        $productLocations = LocationProduct::query()
            ->where('product_id', $id)
            ->where('company_id', $defaultCompany->company_id)
            ->get()
            ->groupBy('location_id')
            ->mapWithKeys(function ($productLocationSections) {
                return [
                    'locations' => [
                        'id' => $productLocationSections->first()->location_id,
                        'name' => $productLocationSections->first()->locations->name,
                        'quantity' => $productLocationSections->sum('in_stock'),
                        'sections' => $productLocationSections->filter(function ($item) {
                            return $item->sub_location_id != null;
                        })->map(function ($locProduct) {
                            return [
                                'id' => $locProduct->section->id,
                                'name' => $locProduct->section->section_name,
                                'quantity' => $locProduct->in_stock
                            ];
                        })
                    ]
                ];
            });

        return new JsonResponse(
            [
                'payload' => [$productLocations]
            ]
        );
    }

    /**
     * Create price
     *
     * Store new price of the specified product.
     * @authenticated
     * @param  ProductAddPriceRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addPrice(ProductAddPriceRequest $request, Product $product): JsonResponse
    {
        $sellingPrice = $this->productService->createProduct($request->all());

        return new JsonResponse(
            new ProductResource($product->load(['category', 'locations', 'unit'])),
            Response::HTTP_OK
        );
    }

    /**
     * Edit price
     *
     * Update price the specified product in storage.
     * @authenticated
     * @param  ProductUpdatePriceRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     * @throws Throwable
     */
    public function updatePrice(ProductUpdatePriceRequest $request, Product $product): JsonResponse
    {
        $sellingPrice = $this->productService->updateSellingPrice($request, $product);

        return new JsonResponse(
            new ProductResource($product->load(['category', 'locations', 'unit'])),
            Response::HTTP_OK
        );
    }

    /**
     * Translations
     *
     * Display translations of the specified product.
     * @authenticated
     * @param  Product  $product
     * @return JsonResponse
     */
    public function getProductTranslations(Product $product): JsonResponse
    {
        $translations = $product->translation;

        return new JsonResponse(['payload' => ProductTranslationResource::collection($translations)]);
    }

    /**
     * Edit location
     *
     * Update location the specified product in storage.
     * @authenticated
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateProductLocation(int $id, Request $request): JsonResponse
    {
        $product = $this->productService->updateInventoryData($id, $request->all());

        return new JsonResponse(
            ['payload' => ProductResource::setMode('inventory')::make($product)],
            Response::HTTP_OK
        );
    }

    /**
     * Delete
     *
     * Remove image of the specified product from storage.
     * @authenticated
     * @param  Media  $image
     * @return void
     */
    public function deleteProductImage(Media $image): void
    {
        $this->productService->deleteImage($image);
    }

    /**
     * Purchase orders
     * 
     * Display purchase orders of the specified product.
     * @authenticated
     * @param Product $product
     * @return JsonResponse
     */
    public function getProductPurchaseOrders(Product $product): JsonResponse
    {
        $purchaseOrders = $this->productService->productPurchaseOrders($product);

        return new JsonResponse(['payload' => ProductPurchaseOrdersResource::collection($purchaseOrders)]);
    }

    public function getProductPriceHistory(Product $product): JsonResponse
    {
        $productPrices = $this->productService->getProductPrices($product);

        return new JsonResponse(['payload' => ProductPriceHistoryResource::collection($productPrices)]);
    }
}
