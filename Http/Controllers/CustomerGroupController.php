<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerGroupResource;
use App\Models\CustomerGroup;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Customer
 *
 * Endpoints for managing customer groups
 */
class CustomerGroupController extends Controller
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
     * Returns list of available customer groups
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            CustomerGroupResource::collection(CustomerGroup::query()->orderBy('id', 'desc')->get())
        );
    }

    /**
     * Create
     *
     * Store a newly created customer group in storage.
     * @authenticated
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show
     *
     * Display the specified customer group.
     * @authenticated
     * @param CustomerGroup $customerGroup
     * @return JsonResponse
     */
    public function show(CustomerGroup $customerGroup): JsonResponse
    {
        return new JsonResponse(['payload' => CustomerGroupResource::make($customerGroup)], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified customer group in storage.
     * @authenticated
     */
    public function update(CustomerGroup $customerGroup, Request $request)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified customer group from storage.
     * @authenticated
     */
    public function destroy(CustomerGroup $customerGroup)
    {
        //
    }
}
