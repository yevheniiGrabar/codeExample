<?php

namespace App\Services;

use App\Models\PaymentTerms;
use App\Traits\CurrentCompany;

class PaymentTermsService
{
    /**
     * @param array $data
     * @return PaymentTerms
     */
    public function createPaymentTerm(array $data): PaymentTerms
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $paymentTerm = new PaymentTerms();

        $paymentTerm->name = $data['name'];
        $paymentTerm->days = $data['days'];
        $paymentTerm->type = $data['type'] ? 1 : 0;
        $paymentTerm->company_id = $currentCompany->company_id;
        $paymentTerm->save();

        return $paymentTerm;
    }

    /**
     * @param $paymentTerm
     * @param array $data
     * @return PaymentTerms
     */
    public function updatePaymentTerm($paymentTerm, array $data): PaymentTerms
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $paymentTerm->update(
            [
                'name' => $data['name'] ?? $paymentTerm->name,
                'days' => $data['days'] ?? $paymentTerm->days,
                'type' => $data['type'] ? 1 : 0,
                'company_id' => $currentCompany->company_id,
            ]
        );
        $paymentTerm->save();

        return $paymentTerm;
    }
}
