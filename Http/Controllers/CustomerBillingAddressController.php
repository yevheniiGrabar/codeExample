<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerBillingAddressStoreRequest;
use App\Http\Resources\BillingAddressResource;
use App\Models\BillingAddress;
use Illuminate\Http\JsonResponse;

/**
 * @group Billing addresses
 *
 * Endpoints for managing billing addresses
 */
class CustomerBillingAddressController extends Controller
{
    /**
     * Create
     *
     * Store a newly created billing address in storage.
     * @authenticated
     * @param CustomerBillingAddressStoreRequest $request
     * @param $customer_id
     * @return JsonResponse
     */
    public function store(CustomerBillingAddressStoreRequest $request, $customer_id): JsonResponse
    {
        $requestData = $request->validated();
        $requestData['customer_id'] = $customer_id;

        return new JsonResponse(
            ['payload' => new BillingAddressResource(BillingAddress::query()->create($requestData))]
        );
    }
}
