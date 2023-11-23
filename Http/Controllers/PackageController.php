<?php

namespace App\Http\Controllers;

use App\Http\Requests\Package\DestroyRequest;
use App\Http\Requests\Package\IndexRequest;
use App\Http\Requests\Package\ShowRequest;
use App\Http\Requests\Package\StoreRequest;
use App\Http\Requests\Package\UpdateRequest;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PackageController extends Controller
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
     * Returns list of available packages
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $packages = Package::query()->orderBy('id', 'desc')->get();

        return $this->dataTransform->conditionalResponse($request, PackageResource::collection($packages));
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
        return new JsonResponse(
            ['payload' => new PackageResource(Package::query()->create($request->validated()))],
            Response::HTTP_CREATED
        );
    }

    /**
     * Show
     *
     * Display the specified category.
     * @authenticated
     * @param Package $package
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Package $package): JsonResponse
    {
        return new JsonResponse(['payload' => PackageResource::make($package)], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified category in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Package $package
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Package $package): JsonResponse
    {
        $package->update($request->validated());
        return new JsonResponse(['payload' => new PackageResource($package)], Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified category from storage.
     * @authenticated
     * @param Package $package
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Package $package): JsonResponse
    {
        $package->delete();

        return new JsonResponse(['message' => 'Package deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
