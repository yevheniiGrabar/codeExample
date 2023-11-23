<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillOfMaterialResource;
use App\Models\BillOfMaterial;
use App\Services\BillOfMaterialService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Bill of material
 *
 * Endpoints for managing bills of material
 */
class BillOfMaterialController extends Controller
{
    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var BillOfMaterialService */
    public BillOfMaterialService $billOfMaterialService;

    public function __construct(JsonResponseDataTransform $dataTransform, BillOfMaterialService $billOfMaterialService)
    {
        $this->dataTransform = $dataTransform;
        $this->billOfMaterialService = $billOfMaterialService;
    }

    /**
     * List
     *
     * Returns list of available bills of material
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            BillOfMaterialResource::collection(
                $this->billOfMaterialService->loadBomRecords($request)
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created bill of material in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $bom = $this->billOfMaterialService->createBOM($request);
        $bomData = $this->billOfMaterialService->loadAdditionalData($bom);

        return new JsonResponse(BillOfMaterialResource::make($bomData));
    }

    /**
     * Show
     *
     * Display the specified bill of material.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $bom = BillOfMaterial::query()->find($id);

        $currentCompany = CurrentCompany::getDefaultCompany();

        if ($bom->company_id != $currentCompany->company_id) {
            return new JsonResponse(['error' => "Access denied"],422);
        }

        return new JsonResponse(['payload' => BillOfMaterialResource::make($this->billOfMaterialService->loadAdditionalData($bom))]);
    }

    /**
     * Edit
     *
     * Update the specified bill of material in storage.
     * @authenticated
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
//        $this->authorize('update', ProductionOrder::class);

        $bom = $this->billOfMaterialService->updateBOM($request, $id);

        return new JsonResponse(new BillOfMaterialResource($bom));
    }

    /**
     * Delete
     *
     * Remove the specified bill of material from storage.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
//        $this->authorize('delete', ProductionOrder::class);
        $bom = BillOfMaterial::query()->find($id);
        $bom->delete();

        return new JsonResponse([]);
    }
}
