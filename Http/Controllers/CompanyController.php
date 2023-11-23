<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\IndexRequest;
use App\Http\Requests\Company\ShowRequest;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\PlanResource;
use App\Models\Company;
use App\Models\Plan;
use App\Services\CompanyService;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Company
 *
 * Endpoints for managing companies
 */
class CompanyController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var CompanyService */
    public CompanyService $companyService;

    public function __construct(JsonResponseDataTransform $dataTransform, CompanyService $companyService)
    {
        $this->dataTransform = $dataTransform;
        $this->companyService = $companyService;
    }

    /**
     * List
     *
     * Returns list of available companies
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function index(IndexRequest $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            CompanyResource::setMode('single')::collection(
                auth()->user()->companies()->wherePivot(
                    'user_id',
                    auth()->id()
                )->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created company in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $newCompany = $this->companyService->createCompany($request->validated());
        $newCompany = $this->companyService->loadAdditionalData($newCompany);

        return new JsonResponse(['payload' => CompanyResource::setMode('details')::make($newCompany)]);
    }

    /**
     * Show
     *
     * Display the specified company.
     * @authenticated
     * @param Company $company
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Company $company): JsonResponse
    {
        $companyData = $this->companyService->getCompany($company);
        $companyData = $this->companyService->loadAdditionalData($company);

        return new JsonResponse(['payload' => CompanyResource::setMode('details')::make($companyData)]);
    }

    /**
     * Edit
     *
     * Update the specified company in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Company $company): JsonResponse
    {
        $companyData = $this->companyService->updateCompany($company, $request->validated());
        $companyData = $this->companyService->loadAdditionalData($company);

        return new JsonResponse(['payload' => CompanyResource::setMode('details')::make($companyData)]);
    }

//    /**
//     * Delete
//     *
//     * Remove the specified company from storage.
//     * @authenticated
//     * @param Company $company
//     * @return JsonResponse
//     * @noinspection PhpPossiblePolymorphicInvocationInspection
//     */
//    public function destroy(Company $company): JsonResponse
//    {
//        //@todo now deleting all from pivot table left delete in company table
//        $company = auth()->user()->companies()->wherePivot('user_id', '=', auth()->user()->id)->detach($company->id);
//        $company->deleteOrFail();
//
//        return new JsonResponse(['message' => 'Company deleted successfully'], Response::HTTP_OK);
//    }


    /**
     * Company plan list
     *
     * Returns list of available user companies plans
     * @authenticated
     * @return JsonResponse
     */
    public function companyPlan(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 422);
        }

        $companies = $user->companies()->wherePivot('user_id', '=', $user->id)->get();

        if (!$companies) {
            return new  JsonResponse(['error' => 'Company not found'], Response::HTTP_NOT_FOUND);
        }

        $plans = [];
        foreach ($companies as $company) {
            $plans[] = Plan::query()->where('company_id', '=', $company->id)->get();

            if (!$plans) {
                return new JsonResponse(['error' => 'Plans not found'], Response::HTTP_NOT_FOUND);
            }
        }

        return new JsonResponse(PlanResource::collection($plans), Response::HTTP_OK);
    }
}
