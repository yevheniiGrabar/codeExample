<?php

namespace App\Http\Controllers;

use App\Http\Requests\Receive\DestroyRequest;
use App\Http\Requests\Receive\IndexRequest;
use App\Http\Requests\Receive\StoreRequest;
use App\Http\Requests\Receive\ShowRequest;
use App\Http\Resources\ReceiveResource;
use App\Models\Receive;
use App\Services\JsonResponseDataTransform;
use App\Services\ReceiveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Receive
 *
 * Endpoints for managing receives
 */
class ReceiveController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var ReceiveService */
    public ReceiveService $receiveService;

    public function __construct(JsonResponseDataTransform $dataTransform, ReceiveService $receiveService)
    {
        $this->dataTransform = $dataTransform;
        $this->receiveService = $receiveService;
    }

    /**
     * List
     *
     * Returns list of available receives
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            ReceiveResource::collection(
                $this->receiveService->getAdditionalData($request)
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created receive in storage.
     * @authenticated
     * @param StoreRequest $receiveStoreRequest
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        //@todo validate data
        $newReceive = $this->receiveService->createNewRecord($request->all());

        return new JsonResponse(new ReceiveResource($newReceive));
    }

    /**
     * Show
     *
     * Display the specified receive.
     * @authenticated
     * @param Receive $receive
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Receive $receive): JsonResponse
    {
        return response()->json([
            'payload' => ReceiveResource::make($receive)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Receive $receive
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Receive $receive): JsonResponse
    {
        $receive->delete();

        return new JsonResponse([]);
    }
}
