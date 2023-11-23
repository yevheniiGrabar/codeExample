<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerDeliveryAddressStoreRequest;
use App\Http\Resources\DeliveryAddressResource;
use App\Models\DeliveryAddress;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Delivery address
 *
 * Endpoints for managing delivery addresses
 */
class CustomerDeliveryAddressController extends Controller
{
    /**
     * Create
     *
     * Store a newly created delivery address in storage.
     * @authenticated
     * @param CustomerDeliveryAddressStoreRequest $request
     * @param $customer_id
     * @return JsonResponse
     */
    public function store(CustomerDeliveryAddressStoreRequest $request, $customer_id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $requestData = $request->validated();
        $requestData['company_id'] = $currentCompany->company_id;
        $requestData['customer_id'] = $customer_id;
        $deliveryAddress = DeliveryAddress::query()->create($requestData);

        return new JsonResponse(
            [
                'payload' => new DeliveryAddressResource($deliveryAddress)
            ], Response::HTTP_CREATED
        );
    }
}
