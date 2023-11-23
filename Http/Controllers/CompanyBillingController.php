<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillingAddressResource;
use App\Http\Resources\CompanyBillingResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\CompanyBilling;
use App\Http\Requests\CompanyBillingStoreRequest;
use App\Http\Requests\CompanyBillingUpdateRequest;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Company billing
 *
 * Endpoints for managing company billings
 */
class CompanyBillingController extends Controller
{
    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * Index
     *
     * Returns company billing.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $billingAddress = CompanyBilling::query()
            ->whereHas('company', function ($query) use ($currentCompany) {
                $query->where('id', $currentCompany->company_id);
            }
            )
            ->get();

        return $this->dataTransform->conditionalResponse($request, CompanyBillingResource::collection($billingAddress));
    }

    /**
     * Create
     *
     * Store a newly created company billing in storage.
     * @authenticated
     * @param CompanyBillingStoreRequest $request
     * @return JsonResponse
     */
    public function store(CompanyBillingStoreRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $company = Company::find($currentCompany->company_id);

        $createData = [
            'name' => $request->input('name'),
            'country_id' => $request->input('country_id'),
            'billing_street' => $request->input('street'),
            'billing_street_2' => $request->input('street_2') ?? null,
            'billing_postal' => $request->input('zipcode') ?? null,
            'billing_city' => $request->input('city') ?? null,
            'billing_email' => $request->input('email') ?? null,
            'billing_phone' => $request->input('phone') ?? null,
            'contact_name' => $request->input('contact_name') ?? null,
            'is_used_for_delivery' => $request->input('is_used_for_delivery') ? 1 : 0,
        ];

        $companyBilling = CompanyBilling::query()->create($createData);
        $company->company_billing_id = $companyBilling->id;
        $company->save();

        return new JsonResponse(['payload' => CompanyBillingResource::make($companyBilling)]);
    }

    /**
     * Show
     *
     * Display the specified company billing.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $billingAddress = CompanyBilling::query()
            ->where('id', $id)
            ->whereHas('company', function ($query) use ($currentCompany) {
                    $query->where('id', $currentCompany->company_id);
                }
            )
            ->first();

        if (!$billingAddress) {
            return new JsonResponse(['error' => 'Billing address not found for the current user\'s company.'], 404);
        }

        return new JsonResponse(['payload' => CompanyBillingResource::make($billingAddress)]);
    }

    /**
     * Edit
     *
     * Update the specified company billing in storage.
     * @authenticated
     * @param CompanyBillingUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CompanyBillingUpdateRequest $request, int $id): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $company = Company::query()
            ->where('id', $currentCompany->company_id)
            ->where('company_billing_id', $id)
            ->first();

        if (!$company) {
            return new JsonResponse(['error' => 'Company billing not found for the current company.'], 404);
        }

        $companyBilling = $company->companyBilling;

        if (!$companyBilling) {
            return new JsonResponse(['error' => 'Company billing not found.'], 404);
        }

        $companyBilling->update(
            [
                'country_id' => $request->input('country_id') ?? $companyBilling->country_id,
                'billing_street' => $request->input('street') ?? $companyBilling->billing_street,
                'billing_street_2' => $request->input('street_2') ?? $companyBilling->billing_street_2,
                'billing_postal' => $request->input('zipcode') ?? $companyBilling->billing_postal,
                'billing_city' => $request->input('city') ?? $companyBilling->billing_city,
                'billing_email' => $request->input('email') ?? $companyBilling->billing_city,
                'billing_phone' => $request->input('phone') ?? $companyBilling->billing_phone,
                'contact_name' => $request->input('contact_name') ?? $companyBilling->contact_name,
                'is_used_for_delivery' => $request->input('is_used_for_delivery') ? 1 : 0,
            ]
        );

        return new JsonResponse(['payload' => new CompanyBillingResource($companyBilling)]);
    }
}
