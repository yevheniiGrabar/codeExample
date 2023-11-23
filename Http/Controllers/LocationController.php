<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\DestroyRequest;
use App\Http\Requests\Location\IndexRequest;
use App\Http\Requests\Location\ShowRequest;
use App\Http\Requests\Location\StoreRequest;
use App\Http\Requests\Location\UpdateRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\JsonResponseDataTransform;
use App\Services\LocationService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @group Location
 *
 * Endpoints for managing locations
 */
class LocationController extends Controller
{
    /** @var LocationService */
    public LocationService $locationService;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(LocationService $locationService, JsonResponseDataTransform $dataTransform)
    {
        $this->locationService = $locationService;
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available locations
     * @authenticated
     * @param  IndexRequest  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        if ($request->has('slim') && $request->get('slim') != false) {
            return new JsonResponse(
                [
                    'payload' => LocationResource::setMode('single')::collection(
                        Location::query()->where('company_id', $defaultCompany->company_id)->get()
                    )
                ]
            );
        }

        return $this->dataTransform->conditionalResponse(
            $request,
            LocationResource::collection(
                Location::query()->where('company_id', $defaultCompany->company_id)->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created location in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
//        $this->authorize(Access::CREATE_LOCATION, Location::class);
        $location = $this->locationService->saveLocation($request);
//        $sections = $this->locationService->saveSubLocation($request);

        return new JsonResponse(['payload' => new LocationResource($location)], Response::HTTP_CREATED);
    }


    /**
     * Show
     *
     * Display the specified location.
     * @authenticated
     * @param  ShowRequest  $request
     * @param  Location  $location
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Location $location): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $data = Location::query()->with(
            [
                'sections' => fn($q) => $q->select(
                    ['id', 'location_id', 'section_name as name', 'row', 'sector', 'shelf_height']
                )
            ]
        )->select(
            'id',
            'name as store',
            'country',
            'city',
            'street',
            'postal as zipcode',
            'contact_name as contactName',
            'phone_number as phoneNumber',
            'email'
        )->where('id', $location->id)->where('company_id', $currentCompany->company_id)->first();

        return new JsonResponse(['payload' => $data]);
    }

    /**
     * Edit
     *
     * Update the specified location in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Location $location
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateRequest $request, Location $location): JsonResponse
    {
        $location = $this->locationService->updateLocationWithSections($request, $location);

        return new JsonResponse(new LocationResource($location), Response::HTTP_ACCEPTED);
    }

    /**
     * Delete
     *
     * Remove the specified location from storage.
     * @authenticated
     * @param  DestroyRequest  $request
     * @param  Location  $location
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Location $location): JsonResponse
    {
        $location->sections()->delete();
        //@todo update this later (now delete record from inventories table with product data)
        //$location->inventories()->delete();
        $location->delete();

        return new JsonResponse(['message' => 'Location Deleted'], Response::HTTP_OK);
    }

    /**
     * Search
     *
     * Display locations by specified filters
     * @authenticated
     * @param null $name
     * @param null $country
     * @param null $city
     * @param null $number
     * @return JsonResponse
     */
    public function search($name = null, $country = null, $city = null, $number = null): JsonResponse
    {
        $results = $this->locationService->search($name, $country, $city, $number);

        return new JsonResponse($results);
    }
}
