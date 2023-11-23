<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Country
 *
 * Endpoints for managing countries
 */
class CountryController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available countries
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            CountryResource::collection(
                Country::query()->where('name', '!=', null)->get()
            )
        );
    }
}
