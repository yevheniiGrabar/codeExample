<?php

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Services;

use Illuminate\Http\Request;

class SaleOrderRequestParser
{
    /**
     * @param Request $request
     * @return array
     */
    public function parseRequest(Request $request): array
    {
        if ($request->has('filters')) {

            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
            $customers = $parsedData->customers ?? [];
            $shipped = $parsedData->shipped ?? '';
            $invoiced = $parsedData->invoiced ?? '';
            $reactive = $request->get('reactive', '');
        }

        return [
            'search ' => $search ?? '',
            'customers' => $customers ?? [],
            'shipped' => $shipped ?? '',
            'invoiced' => $invoiced ?? '',
            'reactive' => $reactive ?? '',

        ];
    }
}
