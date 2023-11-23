<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductTranslationStoreRequest;
use App\Http\Requests\ProductUpdateTranslationRequest;
use App\Http\Resources\ProductTranslationResource;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Services\JsonResponseDataTransform;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Product translation
 *
 * Endpoints for managing product translations
 */
class ProductTranslationController extends Controller
{
    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    protected ProductService $productService;

    public function __construct(JsonResponseDataTransform $dataTransform, ProductService $productService)
    {
        $this->dataTransform = $dataTransform;
        $this->productService = $productService;
    }

    /**
     * List
     *
     * Returns list of available product translations
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            ProductTranslationResource::collection(ProductTranslation::all())
        );
    }

    /**
     * Create
     *
     * Store a newly created product translation in storage.
     * @authenticated
     * @param ProductTranslationStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProductTranslationStoreRequest $request): JsonResponse
    {
        $productTranslation = $this->productService->createProductTranslation($request->validated());

        return new JsonResponse(['payload' => new ProductTranslationResource($productTranslation)]);
    }

    /**
     * Show
     *
     * Display the specified product translation.
     * @authenticated
     * @param ProductTranslation $productTranslation
     * @return JsonResponse
     */
    public function show(ProductTranslation $productTranslation): JsonResponse
    {
        return new JsonResponse(['payload' => new ProductTranslationResource($productTranslation)]);
    }

    /**
     * Edit
     *
     * Update the specified product translation in storage.
     * @authenticated
     * @param ProductUpdateTranslationRequest $request
     * @param ProductTranslation $productTranslation
     * @return JsonResponse
     */
    public function update(ProductUpdateTranslationRequest $request, ProductTranslation $productTranslation): JsonResponse
    {
        $productTranslation = $this->productService->updateProductTranslation($productTranslation, $request->validated());

        return new JsonResponse(['payload' => new ProductTranslationResource($productTranslation)]);
    }

    /**
     * Delete
     *
     * Remove the specified product translation from storage.
     * @authenticated
     * @param ProductTranslation $productTranslation
     * @return JsonResponse
     */
    public function destroy(ProductTranslation $productTranslation): JsonResponse
    {
        $productTranslation->delete();

        return new JsonResponse([]);
    }
}
