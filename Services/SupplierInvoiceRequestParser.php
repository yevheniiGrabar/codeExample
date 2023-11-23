<?php

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Services;

use Illuminate\Http\Request;

class SupplierInvoiceRequestParser
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
            $currency = $parsedData->currency ?? '';
            $status = $parsedData->status ?? '';
            $date = $parsedData->date ?? '';
            $reactive = $request->get('reactive', '');
        }

        return [
            'supplier' => $supplier ?? '',
            'currency' => $currency ?? '',
            'status' => $status ?? '',
            'date' => $date ?? '',
            'reactive' => $reactive ?? ''
        ];
    }
}
