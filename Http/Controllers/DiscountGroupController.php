<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountGroupResource;
use App\Models\DiscountGroup;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Discount group
 *
 * Endpoints for managing discount groups
 */
class DiscountGroupController extends Controller
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
     * Returns list of available discount groups
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            DiscountGroupResource::collection(DiscountGroup::query()->orderBy('id', 'desc')->get())
        );
    }

    /**
     * Create
     *
     * Store a newly created discount group in storage.
     * @authenticated
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json('');
    }

    /**
     * Show
     *
     * Display the specified discount group.
     * @authenticated
     * @param  DiscountGroup  $discountGroup
     * @return JsonResponse
     */
    public function show(DiscountGroup $discountGroup): JsonResponse
    {
        return response()->json('');
    }

    /**
     * Edit
     *
     * Update the specified discount group in storage.
     * @authenticated
     * @param  Request  $request
     * @param  DiscountGroup  $discountGroup
     * @return JsonResponse
     */
    public function update(Request $request, DiscountGroup $discountGroup): JsonResponse
    {
        return response()->json('');
    }

    /**
     * Delete
     *
     * Remove the specified discount group from storage.
     * @authenticated
     * @param DiscountGroup $discountGroup
     * @return JsonResponse
     */
    public function destroy(DiscountGroup $discountGroup): JsonResponse
    {
        return response()->json('');
    }
}
