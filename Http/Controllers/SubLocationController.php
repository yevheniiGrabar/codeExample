<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubLocationResource;
use App\Models\Location;
use App\Models\SubLocation;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Sub location
 *
 * Endpoints for managing sub locations
 */
class SubLocationController extends Controller
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
     * Returns list of available sub locations
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('slim') && $request->get('slim') != false) {
            return new JsonResponse(
                ['payload' => SubLocationResource::setMode('single')::collection(SubLocation::all())]
            );
        }
        return $this->dataTransform->conditionalResponse(
            $request,
            SubLocationResource::collection(
                SubLocation::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created sub location in storage.
     * @authenticated
     * @param Request $request
     * @return SubLocationResource
     */
    public function store(Request $request): SubLocationResource
    {
        $subLocation = SubLocation::query()->create(
            [
                'section_name' => $request->get('section_name'),
                'row' => $request->get('row'),
                'sector' => $request->get('sector'),
                'height' => $request->get('height'),
            ]
        );

        if ($request->has('location_id')) {
            $subLocation->location()->associate($request->get('location_id'));
            $subLocation->save();
        }

        return new SubLocationResource($subLocation);
    }

    /**
     * Show
     *
     * Display the specified sub location.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $subLocation = SubLocation::query()->findOrFail($id);

        return new JsonResponse(SubLocationResource::make($subLocation));
    }

    /**
     * Edit
     *
     * Update the specified sub location in storage.
     * @authenticated
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $subLocation = SubLocation::query()->findOrFail($id);

        if ($request->has('location_id')) {
            $subLocation->update($request->all());
            $subLocation->location()->associate($request->get('location_id'));
            $subLocation->save();
        }

        return new JsonResponse(new SubLocationResource($subLocation));
    }

    /**
     * Delete
     *
     * Remove the specified sub location from storage.
     * @authenticated
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $subLocation = SubLocation::query()->find($id);
        $subLocation->delete();

        if ($request->has('location_id')) {
            $subLocation->location->detach($request->get('location_id'));
        }

        return new JsonResponse(['message' => 'SubLocation deleted successfully']);
    }
}
