<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tax\DestroyRequest;
use App\Http\Requests\Tax\IndexRequest;
use App\Http\Requests\Tax\ShowRequest;
use App\Http\Requests\Tax\StoreRequest;
use App\Http\Requests\Tax\UpdateRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use App\Services\JsonResponseDataTransform;
use App\Services\TaxService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Tax
 *
 * Endpoints for managing taxes
 */
class TaxController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private TaxService $taxService;

    public function __construct(JsonResponseDataTransform $dataTransform, TaxService $taxService)
    {
        $this->dataTransform = $dataTransform;
        $this->taxService = $taxService;
    }

    /**
     * List
     *
     * Returns list of available taxes
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentUserCompany = CurrentCompany::getDefaultCompany();

        if ($request->has('slim') && $request->get('slim') != false) {
            if (!empty($request->get('type'))) {
                $type = $request->get('type');

                $taxes = Tax::query()
                    ->where('company_id', $currentUserCompany->company_id)
                    ->where($type . '_tax', '=',true)
                    ->orderBy('id', 'desc')
                    ->get();

                return new JsonResponse(
                    [
                        'payload' => TaxResource::setMode('single')::collection(
                            $taxes
                        )
                    ]
                );
            } else {
                $taxes = Tax::query()
                    ->where('company_id', $currentUserCompany->company_id)
                    ->orderBy('id', 'desc')
                    ->get();

                return new JsonResponse(['payload' => TaxResource::setMode('single')::collection($taxes)]);
            }
        }

        return $this->dataTransform->conditionalResponse(
            $request,
            TaxResource::collection(
                Tax::query()->where('company_id', $currentUserCompany->company_id)->orderBy('rate', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created tax in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $tax = $this->taxService->createTax($request->validated());

        return new JsonResponse(['payload' => new TaxResource($tax)], Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified tax.
     * @authenticated
     * @param Tax $tax
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Tax $tax): JsonResponse
    {
        return new JsonResponse(['payload' => [TaxResource::make($tax)]], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified tax in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Tax $tax
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Tax $tax): JsonResponse
    {
        $tax = $this->taxService->updateTax($tax, $request->validated());

        return new JsonResponse(['payload' => new TaxResource($tax)], Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified tax from storage.
     * @authenticated
     * @param Tax $tax
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Tax $tax): JsonResponse
    {
        $tax->delete();

        return new JsonResponse(['message' => 'Tax deleted successfully'], Response::HTTP_OK);
    }
}
