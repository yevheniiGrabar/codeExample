<?php

namespace App\Services;

use App\Models\DeliveryTerms;
use App\Traits\CurrentCompany;

class DeliveryTermsService
{
    /**
     * @param array $data
     * @return DeliveryTerms
     */
    public function createDeliveryTerm(array $data): DeliveryTerms
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $deliveryTerm = new DeliveryTerms();

        $deliveryTerm->name = $data['name'];
        $deliveryTerm->description = $data['description'];
        $deliveryTerm->company_id = $currentCompany->company_id;
        $deliveryTerm->save();

        return $deliveryTerm;
    }

    /**
     * @param $deliveryTerms
     * @param array $data
     * @return DeliveryTerms
     */
    public function updateDeliveryTerm($deliveryTerms, array $data): DeliveryTerms
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $deliveryTerms->update([
            'name' => $data['name'] ?? $deliveryTerms->name,
            'description' => $data['description'] ?? $deliveryTerms->description,
            'company_id' => $currentCompany->company_id,
        ]);
        $deliveryTerms->save();

        return $deliveryTerms;
    }
}
