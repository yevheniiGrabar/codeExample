<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentStoreRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\JsonResponseDataTransform;
use App\Services\ShipmondoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as StatusResponse;
use const Grpc\STATUS_ABORTED;

/**
 * @group Shipment
 *
 * Endpoints for managing shipments
 */
class ShipmentController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;
    public ShipmondoService $shipmondoService;

    public function __construct(JsonResponseDataTransform $dataTransform, ShipmondoService $shipmondoService)
    {
        $this->dataTransform = $dataTransform;
        $this->shipmondoService = $shipmondoService;
    }

    /**
     * List
     *
     * Returns list of available shipments
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['payload' => $this->shipmondoService->getAllShipments()], StatusResponse::HTTP_OK);
    }

    /**
     * Create
     *
     * Store a newly created shipment in storage.
     * @authenticated
     * @param ShipmentStoreRequest $request
     * @return JsonResponse
     */
    public function store(ShipmentStoreRequest $request): JsonResponse
    {
        return $this->shipmondoService->createNewShipment($request->validated());
    }

    /**
     * Show
     *
     * Display the specified shipment.
     * @authenticated
     * @param int $shipmentId
     * @return JsonResponse
     */
    public function show(int $shipmentId): JsonResponse
    {
        return new JsonResponse(['payload' => $this->shipmondoService->getShipmentById($shipmentId)], StatusResponse::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified shipment in storage.
     * @authenticated
     * @param Request $request
     * @param Shipment $shipment
     * @return Response
     */
    public function update(Request $request, Shipment $shipment): Response
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified shipment from storage.
     * @authenticated
     * @param Shipment $shipment
     * @return Response
     */
    public function destroy(Shipment $shipment): Response
    {
        //
    }
}
