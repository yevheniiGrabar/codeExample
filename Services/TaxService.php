<?php

namespace App\Services;

use App\Models\Tax;
use App\Traits\CurrentCompany;

class TaxService
{
    /**
     * @param array $data
     * @return Tax
     */
    public function createTax(array $data): Tax
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $tax = new Tax();

        $tax->name = $data['name'];
        $tax->rate = $data['rate'];
        $tax->sale_tax = $data['is_sales_tax'] ? 1 : 0;
        $tax->purchase_tax = $data['is_purchase_tax'] ? 1 : 0;
        $tax->company_id = $currentCompany->company_id;

        $tax->save();

        return $tax;
    }

    /**
     * @param Tax $tax
     * @param array $data
     * @return Tax
     */
    public function updateTax(Tax $tax, array $data): Tax
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $tax->update([
            'name' => $data['name'] ?? $tax->name,
            'rate' => $data['rate'] ?? $tax->rate,
            'sale_tax' => $data['is_purchase_tax'] ? 1 : 0,
            'purchase_tax' => $data['is_purchase_tax'] ? 1 : 0,
            'company_id' => $currentCompany->company_id,
        ]);
        $tax->save();

        return $tax;
    }
}
