<?php

namespace App\Services;

use App\Models\Location;
use App\Models\SubLocation;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationService
{
    public Location $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @param null $name
     * @param null $country
     * @param null $city
     * @param null $number
     * @return JsonResponse
     */
    public function search($name = null, $country = null, $city = null, $number = null): JsonResponse
    {
        $results = Location::query()
            ->where('name', 'LIKE', '%' . $name . '%')
            ->orWhere('number', 'LIKE', '%' . $number . '%')
            ->orWhere('country', 'LIKE', '%' . $country . '%')
            ->orWhere('city', 'LIKE', '%' . $city . '%')
            ->get();

        if (count($results)) {
            return new JsonResponse($results);
        } else {
            return new JsonResponse(['Results' => 'No data found']);
        }
    }

    /**
     * @param Request $request
     * @return Location
     */
    public function saveLocation(Request $request): Location
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $location = new Location();
        $location->name = $request->get('name');
        $location->country = $request->get('country');
        $location->city = $request->get('city');
        $location->street = $request->get('street');
        $location->postal = $request->get('zipcode');
        $location->contact_name = $request->get('contactName');
        $location->phone_number = $request->get('phone');
        $location->email = $request->get('email');
        $location->company_id = $currentCompany->company_id;

        if ($request->has('sections')) {
            $location->has_sub_location = '1';
        }
        $location->save();

        $subLocation = '';

        if ($request->has('sections') && sizeof($request->get('sections')) > 0) {
            $sections = $request->get('sections');
            foreach ($sections as $key => $section) {
                $subLocation = new SubLocation();
                $subLocation->section_name = $section['name'];
                $subLocation->sector = $section['sector'];
                $subLocation->row = $section['row'];
                $subLocation->shelf_height = $section['shelf_height'];
                $subLocation->location_id = $location->id;
                $subLocation->save();
            }
        }

        return $location;
    }

    /**
     * @param Request $request
     * @param Location $location
     * @return Location
     */
    public function updateLocationWithSections(Request $request, Location $location): Location
    {
        $company =  CurrentCompany::getDefaultCompany();
        //update existing Location
        $location->update(
            [
                'name' => $request->get('name'),
                'country' => $request->get('country'),
                'city' => $request->get('city'),
                'street' => $request->get('street'),
                'postal' => $request->get('zipcode'),
                'contact_name' => $request->get('contactName'),
                'phone_number' => $request->get('phone'),
                'email' => $request->get('email'),
                'company_id' => $company->company_id,
            ]
        );

        //get sections from request
        $sectionsData = $request->get('sections');

        //check sectionData from request
        if (!empty($sectionsData) && is_array($sectionsData)) {
            //get related subLocation
            $existingSections = $location->sections()->get();

            //prepare subLocation ids to delete
            $sectionIdsToDelete = array_diff(
                $existingSections->pluck('id')->toArray(),
                array_column($sectionsData, 'id')
            );

            //check if !empty array of subLocations  ids
            if (!empty($sectionIdsToDelete)) {
                //delete subLocations
                $subLocations =SubLocation::query()
                    ->whereIn('id', $sectionIdsToDelete)
//                    ->where('quantity', '<=', 0)
                    ->delete();

            }

            //get one record from request
            foreach ($sectionsData as $sectionData) {
                //check if new record or not (if new sectionData without Id)
                if (!empty($sectionData['id'])) {
                    //find existing subLocation
                    $existingSection = SubLocation::query()->findOrFail($sectionData['id']);
                    //update existing subLocation
                    $existingSection->update(
                        [
                            'section_name' => $sectionData['name'],
                            'sector' => $sectionData['sector'],
                            'row' => $sectionData['row'],
                            'shelf_height' => $sectionData['shelf_height'],
                        ]
                    );
                } else {
                    //create new SubLocation
                    $newSection = SubLocation::query()->create(
                        [
                            'section_name' => $sectionData['name'],
                            'sector' => $sectionData['sector'],
                            'row' => $sectionData['row'],
                            'shelf_height' => $sectionData['shelf_height'],
                        ]
                    );
                    //associate new subLocation with existing Location
                    $location->sections()->save($newSection);
                }
            }
        }

        return $location;
    }
}
