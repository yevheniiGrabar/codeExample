<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Services;

use Illuminate\Http\Request;

class PurchaseOrderRequestParser
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
          $suppliers = $parsedData->suppliers ?? [];
          $invoiced = $parsedData->invoiced ?? '';
          $received = $parsedData->received ?? '';
            $dateFrom = $parsedData->delivery_dates->from ?? '';
            $dateTo = $parsedData->delivery_dates->to ?? '';
          $reactive = $request->get('reactive', '');
        }

        return [
            'suppliers' => $suppliers ?? [],
            'invoiced' => $invoiced ?? '',
            'received' => $received ?? '',
            'from' => $dateFrom ?? '',
            'to' => $dateTo ?? '',
            'reactive' => $reactive ?? '',
            'search' => $search ?? ''
        ];
    }
}
