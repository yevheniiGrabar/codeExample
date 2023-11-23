<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Industry
 *
 * Endpoints for managing industries
 */
class IndustryController extends Controller
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
     * Returns list of available industries
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request, Industry::query()->orderBy('id', 'desc')->get()
        );
    }

    /**
     * Create
     *
     * Store a newly created industry in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $industry = Industry::query()->create($request->all());

        return new JsonResponse(['payload' => $industry]);
    }

    /**
     * Show
     *
     * Display the specified industry.
     * @authenticated
     * @param Industry $industry
     * @return JsonResponse
     */
    public function show(Industry $industry): JsonResponse
    {
        return new JsonResponse($industry);
    }

    /**
     * Edit
     *
     * Update the specified industry in storage.
     * @authenticated
     * @param Request $request
     * @param Industry $industry
     * @return JsonResponse
     */
    public function update(Request $request, Industry $industry): JsonResponse
    {
        $industry->update($request->all());

        return new JsonResponse($industry);
    }

    /**
     * Delete
     *
     * Remove the specified industry from storage.
     * @authenticated
     * @param Industry $industry
     * @return JsonResponse
     */
    public function destroy(Industry $industry)
    {
        $industry->delete();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
