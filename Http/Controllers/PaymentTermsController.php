<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTerms\DestroyRequest;
use App\Http\Requests\PaymentTerms\IndexRequest;
use App\Http\Requests\PaymentTerms\ShowRequest;
use App\Http\Requests\PaymentTerms\StoreRequest;
use App\Http\Requests\PaymentTerms\UpdateRequest;
use App\Http\Resources\PaymentTermsResource;
use App\Models\PaymentTerms;
use App\Services\JsonResponseDataTransform;
use App\Services\PaymentTermsService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * @group Payment terms
 *
 * Endpoints for managing payment terms
 */
class PaymentTermsController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;
    public PaymentTermsService $paymentTermsService;

    public function __construct(JsonResponseDataTransform $dataTransform, PaymentTermsService $paymentTermsService)
    {
        $this->dataTransform = $dataTransform;
        $this->paymentTermsService = $paymentTermsService;
    }

    /**
     * List
     *
     * Returns list of available payment terms
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        return $this->dataTransform->conditionalResponse(
            $request,
            PaymentTermsResource::collection(
                PaymentTerms::query()
                    ->where('company_id', $currentCompany->company_id)
                    ->orderBy('id', 'desc')
                    ->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created payment term in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $paymentTerm = $this->paymentTermsService->createPaymentTerm($request->validated());


        return new JsonResponse(new PaymentTermsResource($paymentTerm), ResponseAlias::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified payment term.
     * @authenticated
     * @param PaymentTerms $payment_term
     * @return JsonResponse
     */
    public function show(ShowRequest $request, int $id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $payment_term = PaymentTerms::query()->where('company_id', $currentCompany->company_id)->find($id);

        if (!$payment_term || $payment_term->company_id !== $currentCompany->company_id) {
            return new JsonResponse(['error' => 'Access denied']);
        }

        return new JsonResponse(['payload' => PaymentTermsResource::make($payment_term)]);
    }

    /**
     * Edit
     *
     * Update the specified payment term in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $payment_term = PaymentTerms::query()->where('company_id', $currentCompany->company_id)->find($id);

        if (!$payment_term || $payment_term->company_id !== $currentCompany->company_id) {
            return new JsonResponse(['error' => 'Access denied']);
        }

        $updatedPayments = $this->paymentTermsService->updatePaymentTerm($payment_term, $request->validated());

        return new JsonResponse(['payload' => new PaymentTermsResource($updatedPayments)], ResponseAlias::HTTP_OK);
    }

    /**
     * Delete
     *
     * Remove the specified payment term from storage.
     * @authenticated
     * @param PaymentTerms $payment_term
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, PaymentTerms $payment_term): JsonResponse
    {
        try {
            $payment_term->deleteOrFail();
            return new JsonResponse(['message' => 'Payment term has been deleted.'], ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            $e->getMessage();
        }
        return new JsonResponse([]);
    }
}
