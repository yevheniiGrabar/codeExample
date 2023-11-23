<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyLanguage\DestroyRequest;
use App\Http\Requests\CompanyLanguage\IndexRequest;
use App\Http\Requests\CompanyLanguage\StoreRequest;
use App\Http\Requests\LanguageStoreRequest;
use App\Http\Requests\LanguageUpdateRequest;
use App\Http\Resources\LanguageResource;
use App\Models\Company;
use App\Models\Language;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Language
 *
 * Endpoints for managing languages
 */
class CompanyLanguageController extends Controller
{
    use CurrentCompany;

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
     * Returns list of available languages for current company
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $companyLanguages = Company::query()->find($currentCompany->company_id)->languages;

        return $this->dataTransform->conditionalResponse($request, LanguageResource::collection($companyLanguages));
    }

    /**
     * Create
     *
     * Associate existing languages with current user's company
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $pivotData = $this->getDefaultCompany();

        $company = Company::query()->find($pivotData->company_id);

        $languageId = $request->get('language_id');

        if ($company->languages()->where('language_id', $languageId)->exists()) {
            return new JsonResponse(['error' => 'This language is already selected for the company.'], 400);
        }

        $language = Language::query()->find($languageId);

        if ($language) {
            $company->languages()->attach($languageId);
        } else {
            return new JsonResponse(['error' => 'Language not found'], 404);
        }

        return new JsonResponse(['payload' => LanguageResource::make($language)]);
    }

//    /**
//     * Show
//     *
//     * Display the specified language by id.
//     * @authenticated
//     * @param int $id
//     * @return JsonResponse
//     */
//    public function show(int $id): JsonResponse
//    {
//        $pivotData = $this->getDefaultCompany();
//        $company = Company::query()->find($pivotData->company_id);
//
//        $language = $company->languages()->find($id);
//
//        if ($language) {
//            return new JsonResponse(['payload' => LanguageResource::make($language)]);
//        } else {
//            return new JsonResponse(['error' => 'Language not found for this company'], 404);
//        }
//    }

    /**
     * Delete
     *
     * Detach the specified language from current user's company.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, int $id): JsonResponse
    {
        $pivotData = $this->getDefaultCompany();
        $company = Company::query()->find($pivotData->company_id);

        $company->languages()->detach($id);


        return new JsonResponse(['message' => 'Language deleted successfully']);
    }
}
