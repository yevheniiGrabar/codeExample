<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDeliveryAddressStoreRequest;
use App\Http\Requests\CompanyDeliveryAddressUpdateRequest;
use App\Http\Resources\DeliveryAddressResource;
use App\Models\DeliveryAddress;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyDeliveryAddressController extends Controller
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
     * Returns list of available delivery addresses
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        return $this->dataTransform->conditionalResponse(
            $request,
            DeliveryAddressResource::collection(
                DeliveryAddress::query()
                    ->where('company_id', $currentCompany->company_id)
                    ->orderBy('id', 'desc')
                    ->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created delivery address in storage.
     * @authenticated
     * @param CompanyDeliveryAddressStoreRequest $request
     * @return JsonResponse
     */
    public function store(CompanyDeliveryAddressStoreRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $requestData = $request->validated();
        $requestData['company_id'] = $currentCompany->company_id;

        $deliveryAddress = DeliveryAddress::query()->create($requestData);

        return new JsonResponse(
            [
                'payload' => new DeliveryAddressResource($deliveryAddress)
            ], Response::HTTP_CREATED
        );
    }

    /**
     * Show
     *
     * Display the specified delivery address.
     * @authenticated
     * @param DeliveryAddress $deliveryAddress
     * @return JsonResponse
     */
    public function show(DeliveryAddress $deliveryAddress): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        if ($deliveryAddress->company_id != $currentCompany->company_id) {
            return new JsonResponse(
                ['error' => 'Company address not found for current company.'],
                Response::HTTP_NOT_FOUND
            );
        }
        return new JsonResponse(DeliveryAddressResource::make($deliveryAddress), Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified delivery address in storage.
     * @authenticated
     * @param CompanyDeliveryAddressUpdateRequest $request
     * @param DeliveryAddress $deliveryAddress
     * @return JsonResponse
     */
    public function update(CompanyDeliveryAddressUpdateRequest $request, DeliveryAddress $deliveryAddress): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        if ($deliveryAddress->company_id !== $currentCompany->company_id) {
            return new JsonResponse(
                ['error' => 'Delivery address does not belong to the current user\'s company.'], 403
            );
        }

        $requestData = $request->validated();
        $requestData['company_id'] = $currentCompany->company_id;

        if ($deliveryAddress->update($requestData)) {
            return new JsonResponse(
                DeliveryAddressResource::make($deliveryAddress),
                Response::HTTP_ACCEPTED
            );
        } else {
            return new JsonResponse(['error' => 'Failed to update delivery address.'], 500);
        }
    }

    /**
     * Delete
     *
     * Remove the specified delivery address from storage.
     * @authenticated
     * @param DeliveryAddress $deliveryAddress
     * @return JsonResponse
     */
    public function destroy(DeliveryAddress $deliveryAddress): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        if ($deliveryAddress->company_id !== $currentCompany->company_id) {
            return new JsonResponse(['error' => 'Delivery address does not belong to the current user\'s company.'], 403);
        }

        $deliveryAddress->delete();

        return new JsonResponse(['message' => 'DeliveryAddress deleted successfully'], Response::HTTP_OK);
    }
}
