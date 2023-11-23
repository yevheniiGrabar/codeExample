<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Services\JsonResponseDataTransform;
use App\Services\UnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Unit
 *
 * Endpoints for managing units
 */
class UnitController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private UnitService $unitService;

    public function __construct(JsonResponseDataTransform $dataTransform, UnitService $unitService)
    {
        $this->dataTransform = $dataTransform;
        $this->unitService = $unitService;
    }

    /**
     * List
     *
     * Returns list of available units
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            UnitResource::collection(
                Unit::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created unit in storage.
     * @authenticated
     * @param UnitStoreRequest $request
     * @return JsonResponse
     */
    public function store(UnitStoreRequest $request): JsonResponse
    {
        $unit = $this->unitService->createUnit($request->validated());

        return new JsonResponse(new UnitResource($unit));
    }

    /**
     * Show
     *
     * Display the specified unit.
     * @authenticated
     * @param Unit $unit
     * @return JsonResponse
     */
    public function show(Unit $unit): JsonResponse
    {
        return new JsonResponse(['payload' => UnitResource::make($unit)]);
    }


    /**
     * Edit
     *
     * Update the specified unit in storage.
     * @authenticated
     * @param UnitUpdateRequest $request
     * @param Unit $unit
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UnitUpdateRequest $request, Unit $unit): JsonResponse
    {
        $unit = $this->unitService->updateUnit($unit, $request->all());

        return new JsonResponse(UnitResource::make($unit));
    }

    /**
     * Delete
     *
     * Remove the specified unit from storage.
     * @authenticated
     * @param Unit $unit
     * @return JsonResponse
     */
    public function destroy(Unit $unit): JsonResponse
    {
        $unit->delete();

        return new JsonResponse([]);
    }
}
