<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionStoreRequest;
use App\Http\Requests\CollectionUpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Collection
 *
 * Endpoints for managing categories
 */
class CollectionController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available collections
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        return $this->dataTransform->conditionalResponse(
            $request,
            CollectionResource::collection(
                Collection::query()->where('company_id', $defaultCompany->company_id)->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created collection in storage.
     * @authenticated
     * @param CollectionStoreRequest $request
     * @return JsonResponse
     */
    public function store(CollectionStoreRequest $request): JsonResponse
    {
        $collection = Collection::query()->updateOrCreate($request->validated());

        return new JsonResponse(new CollectionResource($collection), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified collection.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $collection = Collection::query()->find($id);
        $collection->with('products:id,name')->get();

        return new JsonResponse(['payload' => [CollectionResource::make($collection)]], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified collection in storage.
     * @authenticated
     * @param CollectionUpdateRequest $request
     * @param Collection $collection
     * @return JsonResponse
     */
    public function update(CollectionUpdateRequest $request, Collection $collection): JsonResponse
    {
        $collection->update($request->validated());

        return new JsonResponse(new CollectionResource($collection), Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified collection from storage.
     * @authenticated
     * @param Collection $collection
     * @return JsonResponse
     */
    public function destroy(Collection $collection): JsonResponse
    {
        $collection->delete();

        return new JsonResponse(['message' => 'Model deleted successfully'], Response::HTTP_OK);
    }
}
