<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingAddressResource;
use App\Models\ShippingAddress;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Shipping address
 *
 * Endpoints for managing shipping address
 */
class ShippingAddressController extends Controller
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
     * Returns list of available shipping addresss
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // @todo refactor display only current company shipping_addresses & add authorize method

        return $this->dataTransform->conditionalResponse(
            $request,
            ShippingAddressResource::collection(ShippingAddress::query()->orderBy('id', 'desc')->get())
        );
    }

    /**
     * Create
     *
     * Store a newly created shipping address in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $shippingAddress = new ShippingAddress($request->all());

        return new JsonResponse(new ShippingAddressResource($shippingAddress));
    }

    /**
     * Show
     *
     * Display the specified shipping address.
     * @authenticated
     * @param ShippingAddress $shippingAddress
     * @return JsonResponse
     */
    public function show(ShippingAddress $shippingAddress): JsonResponse
    {
        return new JsonResponse(ShippingAddressResource::make($shippingAddress));
    }

    /**
     * Edit
     *
     * Update the specified shipping address in storage.
     * @authenticated
     * @param Request $request
     * @param ShippingAddress $shippingAddress
     * @return JsonResponse
     */
    public function update(Request $request, ShippingAddress $shippingAddress): JsonResponse
    {
        $shippingAddress->update($request->all());

        return new JsonResponse(ShippingAddressResource::make($shippingAddress));
    }

    /**
     * Delete
     *
     * Remove the specified shipping address from storage.
     * @authenticated
     * @param ShippingAddress $shippingAddress
     * @return JsonResponse
     */
    public function destroy(ShippingAddress $shippingAddress): JsonResponse
    {
        $shippingAddress->delete();

        return new JsonResponse([]);
    }
}
