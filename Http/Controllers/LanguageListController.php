<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageListController extends Controller
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
     * List of all languages
     *
     * Return list of  all available languages
     * @param Request $request
     * @return JsonResponse
     */
    public function displayListOfAllLanguages(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse($request, LanguageResource::collection(Language::all()));
    }
}
