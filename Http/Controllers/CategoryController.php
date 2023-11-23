<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\DestroyRequest;
use App\Http\Requests\Category\IndexRequest;
use App\Http\Requests\Category\ShowRequest;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @group Categories
 *
 * Endpoints for managing categories
 */
class CategoryController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    private CategoryService $categoryService;

    public function __construct(JsonResponseDataTransform $dataTransform, CategoryService $categoryService)
    {
        $this->dataTransform = $dataTransform;
        $this->categoryService = $categoryService;
    }

    /**
     * List
     * 
     * Returns list of available categories
     * @authenticated 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        return $this->dataTransform->conditionalResponse(
            $request,
            CategoryResource::collection(
                Category::query()->where('company_id', $currentCompany->company_id)->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created category in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return new JsonResponse(['payload' => new CategoryResource($category)], Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified category.
     * @authenticated
     * @param Category $category
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Category $category): JsonResponse
    {
        return new JsonResponse(['payload' => CategoryResource::make($category)], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified category in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Category $category
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->updateCategory($category, $request->all());

        return new JsonResponse(
            new CategoryResource($category),
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Delete
     *
     * Remove the specified category from storage.
     * @authenticated
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Category $category): JsonResponse
    {
        $category->delete();

        return new JsonResponse(['message' => 'Deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
