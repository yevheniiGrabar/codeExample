<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Http\Requests\UpdatePlanRequest;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Plan
 *
 * Endpoints for managing plans
 */
class PlanController extends Controller
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
     * List
     *
     * Returns list of available plans
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse($request, PlanResource::collection(Plan::all()));
    }

    /**
     * Create
     *
     * Store a newly created plan in storage.
     * @authenticated
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
//        $fields = $request->validate([
//            'license' => 'required|string',
//            'support' => 'required|string',
//
//            'license_end' => 'required|date',
//            'support_end' => 'required|date'
//        ]);

        // add company id to the field too
        // ? auth()->user()->company returns null
//        $fields['company_id'] = auth()->user()->company->id;

        // create a plan based on validated fields
//        $new_plan = Plan::create($fields);

//        return response()->json($new_plan, 200, [], JSON_PRETTY_PRINT);
    }

    //@TODO move this action to company controller & rework

    /**
     * User`s company plan
     *
     * Return authenticated user's company's plan
     * @authenticated
     */
    public function companyPlan(): JsonResponse
    {
        if (Auth::id()) {
            if (auth()->user()->company) {
                $plan = Plan::where('company_id', Auth::user()->company->id)->first();
            } else {
                return response()->json(["message" => "No company found"], 404, [], JSON_PRETTY_PRINT);
            }
        } else {
            return response()->json(["message" => "Not authenticated"], 403, [], JSON_PRETTY_PRINT);
        }

        return response()->json($plan, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Show
     *
     * Display the specified plan.
     * @authenticated
     * @param Plan $plan
     * @return JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        return new JsonResponse(['payload' => PlanResource::make($plan)]);
    }

    /**
     * Edit
     *
     * Update the specified plan in storage.
     * @authenticated
     * @param Request $request
     * @param Plan $plan
     * @return Response
     */
    public function update(Request $request, Plan $plan): Response
    {
    }

    /**
     * Delete
     *
     * Remove the specified plan from storage.
     * @authenticated
     * @param Plan $plan
     * @return Response
     */
    public function destroy(Plan $plan): Response
    {
        //
    }
}
