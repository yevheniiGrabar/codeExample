<?php

namespace App\Services;

use Illuminate\Http\Request;

class InventoryStockMovementRequestParser
{
    /**
     * @param Request $request
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function parseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
            $products = $parsedData->products ?? [];
            $users = $parsedData->users ?? [];
            $location_from = $parsedData->location_from ?? [];
            $location_to = $parsedData->location_to ?? [];
            $remarks = $parsedData->remarks ?? '';
            $dateFrom = $parsedData->date->from ?? '';
            $dateTo = $parsedData->date->to ?? '';
        }

        return [
            'search' => $search ?? '',
            'products' => $products ?? [],
            'users' => $users ?? [],
            'location_from' => $location_from ?? [],
            'location_to' => $location_to ?? [],
            'remarks' => $remarks ?? '',
            'from' => $dateFrom ?? '',
            'to' => $dateTo ?? '',
        ];
    }
}
