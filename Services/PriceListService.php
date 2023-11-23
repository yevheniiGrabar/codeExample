<?php

namespace App\Services;

use App\Models\PriceList;

class PriceListService
{
    /**
     * @param array $data
     * @return PriceList
     */
    public function createPriceList(array $data): PriceList
    {
        $priceList = new PriceList();

        $priceList->name = $data['name'];
        $priceList->code = $data['code'];
        $priceList->currency_id = $data['currency'];

        $priceList->save();

        return $priceList;
    }

    /**
     * @param PriceList $priceList
     * @param array $data
     * @return PriceList
     */
    public function updatePriceList(PriceList $priceList, array $data): PriceList
    {
        $priceList->update([
            'name' => $data['name'] ?? $priceList->name,
            'code' => $data['code'] ?? $priceList->code,
            'currency_id' => $data['currency'] ?? $priceList->currency_id,
        ]);

        $priceList->save();

        return $priceList;
    }
}
