<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryTerms\DestroyRequest;
use App\Http\Requests\DeliveryTerms\IndexRequest;
use App\Http\Requests\DeliveryTerms\ShowRequest;
use App\Http\Requests\DeliveryTerms\StoreRequest;
use App\Http\Requests\DeliveryTerms\UpdateRequest;
use App\Http\Resources\DeliveryTermsResource;
use App\Models\DeliveryTerms;
use App\Services\DeliveryTermsService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * @group Delivery terms
 *
 * Endpoints for managing delivery terms
 */
class DeliveryTermsController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;
    public DeliveryTermsService $deliveryTermsService;

    public function __construct(JsonResponseDataTransform $dataTransform, DeliveryTermsService $deliveryTermsService)
    {
        $this->dataTransform = $dataTransform;
        $this->deliveryTermsService = $deliveryTermsService;
    }

    /**
     * List
     *
     * Returns list of available delivery terms
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        return $this->dataTransform->conditionalResponse(
            $request,
            DeliveryTermsResource::collection(
                DeliveryTerms::query()
                    ->where('company_id', $currentCompany->company_id)
                    ->orderBy('id', 'desc')
                    ->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created delivery terms in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $deliveryTerm = $this->deliveryTermsService->createDeliveryTerm($request->validated());

        return new JsonResponse(new DeliveryTermsResource($deliveryTerm), ResponseAlias::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified delivery terms.
     * @authenticated
     * @param DeliveryTerms $delivery_term
     * @return JsonResponse
     */
    public function show(ShowRequest $request, int $id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $delivery_term = DeliveryTerms::query()->where('company+_id', $currentCompany->company_id)->find($id);

        if (!$delivery_term || $delivery_term->company_id !== $currentCompany->company_id) {
            return new JsonResponse(['error' => 'Access denied']);
        }
        return new JsonResponse(['payload' => DeliveryTermsResource::make($delivery_term)], ResponseAlias::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified delivery terms in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $delivery = DeliveryTerms::query()->where('company_id', $currentCompany->company_id)->find($id);

        if (!$delivery || $delivery->company_id !== $currentCompany->company_id) {
            return new JsonResponse(['error' => 'Access denied']);
        }

        $delivery_term = $this->deliveryTermsService->updateDeliveryTerm($delivery, $request->validated());

        return new JsonResponse(['payload' => new DeliveryTermsResource($delivery_term)], ResponseAlias::HTTP_OK);
    }

    /**
     * Delete
     *
     * Remove the specified delivery terms from storage.
     * @authenticated
     * @param DeliveryTerms $delivery_term
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, DeliveryTerms $delivery_term): JsonResponse
    {
        try {
            $delivery_term->delete();
            return new JsonResponse(['message' => 'Delivery term has been deleted.'], ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            $e->getMessage();
        }
        return new JsonResponse([]);
    }
}
