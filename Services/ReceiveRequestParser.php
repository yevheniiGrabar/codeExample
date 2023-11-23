<?php

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Services;

use Illuminate\Http\Request;

class ReceiveRequestParser
{
    /**
     * @param Request $request
     * @return array
     */
    public function parseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));


            $supplier = $parsedData->supplier ?? '';
            $receiveDate = $parsedData->receiveDate ?? '';
            $reactive = $request->get('reactive', '');
        }

        return [
            'supplier' => $supplier ?? '',
            'receiveDate' => $receiveDate ?? '',
            'reactive' => $reactive ?? ''
        ];
    }
}
